// ==========================================================================
// ESP32 Irrigation Controller Firmware
// ==========================================================================

// --------------------------------------------------------------------------
// Includes
// --------------------------------------------------------------------------
#include "config.h"
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <NTPClient.h>
#include <WiFiUdp.h>
#include <FS.h>
#include <LittleFS.h>
#include <Wire.h>
#include <RTClib.h>

// --------------------------------------------------------------------------
// Global Variables
// --------------------------------------------------------------------------

// RTC instance (always declared, only used if ENABLE_RTC is true)
RTC_DS3231 rtc;
bool rtcFound = false;
bool rtcTimeReliable = false;

// WiFi and NTP
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, NTP_SERVER, GMT_OFFSET_SEC, DAYLIGHT_OFFSET_SEC);

// Valve pins array
const int VALVE_PINS[NUM_VALVES] = {VALVE_PIN_1, VALVE_PIN_2, VALVE_PIN_3, VALVE_PIN_4, VALVE_PIN_5};

// Scheduling variables
unsigned long valveActivationDurationMillis = VALVE_DURATION_MINUTES * 60 * 1000;
int currentValveInSequence = -1;
unsigned long currentValveStartTime = 0;
bool systemTimeSynched = false;
bool scheduledCycleToday = false;

// System status
unsigned long lastHeartbeat = 0;
unsigned long lastConfigSync = 0;
unsigned long lastCommandPoll = 0;


// --------------------------------------------------------------------------
// Funções Auxiliares (protótipos)
// --------------------------------------------------------------------------
void connectWiFi();
void syncTimeNTP();
void initRTC();
void syncRtcWithNtp();
DateTime getCurrentTime(); // Retorna DateTime, prioriza NTP, fallback para RTC
bool isSystemTimeReliable(); // Verifica se NTP ou RTC confiável estão disponíveis
String formatDateTime(const DateTime& dt); // Formata DateTime para String

void initFileSystem();
void appendLogToFile(const String& message);

void sendStatusToAPI(int valveNumber, bool state); // Renomeado para state (boolean)
void sendLogToAPI(String level, String message, JsonObjectConst details); // JsonObjectConst para ler detalhes
void controlValve(int valveNumber, bool state);
void processScheduledIrrigation();

// Placeholders para Comandos Manuais
void processIncomingCommands();
void manualValveControl(int valveNumber, bool state, String source = "UNKNOWN");
void manualStartFullCycle(String source = "UNKNOWN");
void manualStopAllValves(String source = "UNKNOWN");

// ==========================================================================
// SETUP - Executado uma vez no arranque
// ==========================================================================
void setup() {
    Serial.begin(115200);
    while (!Serial) { delay(10); } // Espera o Serial estar pronto
    Serial.println("\n[SETUP] Iniciando Controlador de Irrigacao ESP32...");

    // Inicializar sistema de ficheiros (LittleFS)
    initFileSystem();

    // Inicializar pinos das válvulas como OUTPUT e desligá-las
    Serial.println("[SETUP] Configurando pinos das valvulas...");
    for (int i = 0; i < NUM_VALVES; i++) {
        pinMode(VALVE_PINS[i], OUTPUT);
        digitalWrite(VALVE_PINS[i], RELAY_OFF_STATE); // Garantir que todas as válvulas começam desligadas
    }
    Serial.println("[SETUP] Pinos das valvulas configurados.");

    // Conectar ao Wi-Fi
    connectWiFi();

    // Inicializar RTC (se habilitado)
    #if ENABLE_RTC
    initRTC();
    #endif

    // Primeira sincronização de tempo
    syncTimeNTP();

    // Primeira sincronização de configuração
    // getConfigFromAPI();

    Serial.println("[SETUP] Sistema pronto!");
}

// ==========================================================================
// LOOP - Executado continuamente
// ==========================================================================
void loop() {
    // Manter conexão WiFi
    if (WiFi.status() != WL_CONNECTED) {
        connectWiFi();
    }

    // Atualizar cliente NTP
    timeClient.update();

    // Processar irrigação agendada
    processScheduledIrrigation();

    // Verificar comandos manuais via API (Polling)
    if (millis() - lastCommandPoll >= COMMAND_POLL_INTERVAL_MS) {
        processIncomingCommands();
        lastCommandPoll = millis();
    }

    // Sincronizar hora com NTP periodicamente
    static unsigned long lastTimeSync = 0;
    if (millis() - lastTimeSync >= TIME_SYNC_INTERVAL_MS) {
        syncTimeNTP();
        lastTimeSync = millis();
    }

    delay(MAIN_LOOP_DELAY_MS);
}

// ==========================================================================
// Funções de Comunicação e Lógica
// ==========================================================================

void connectWiFi() {
    if (WiFi.status() == WL_CONNECTED) return;

    Serial.print("[WIFI] Conectando a ");
    Serial.println(WIFI_SSID);

    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

    unsigned long startAttemptTime = millis();

    while (WiFi.status() != WL_CONNECTED && millis() - startAttemptTime < WIFI_TIMEOUT_MS) {
        delay(500);
        Serial.print(".");
    }

    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\n[WIFI] Conectado!");
        Serial.print("[WIFI] Endereco IP: ");
        Serial.println(WiFi.localIP());
    } else {
        Serial.println("\n[WIFI] Falha na conexao. Operando em modo offline.");
    }
}

void syncTimeNTP() {
    if (WiFi.status() != WL_CONNECTED) return;

    Serial.println("[TIME] Sincronizando com NTP...");
    if (timeClient.forceUpdate()) {
        systemTimeSynched = true;
        Serial.print("[TIME] Hora atualizada: ");
        Serial.println(timeClient.getFormattedTime());

        #if ENABLE_RTC
        syncRtcWithNtp();
        #endif
    } else {
        Serial.println("[TIME] Erro ao sincronizar com NTP.");
    }
}

void initRTC() {
    if (!rtc.begin()) {
        Serial.println("[RTC] Nao foi possivel encontrar o RTC");
        rtcFound = false;
        return;
    }

    rtcFound = true;
    if (rtc.lostPower()) {
        Serial.println("[RTC] RTC perdeu alimentacao, definindo hora padrao!");
        rtc.adjust(DateTime(F(__DATE__), F(__TIME__)));
        rtcTimeReliable = false;
    } else {
        rtcTimeReliable = true;
    }
}

void syncRtcWithNtp() {
    if (rtcFound && systemTimeSynched) {
        rtc.adjust(DateTime(timeClient.getEpochTime()));
        rtcTimeReliable = true;
        Serial.println("[RTC] RTC sincronizado com NTP.");
    }
}

DateTime getCurrentTime() {
    if (systemTimeSynched) {
        return DateTime(timeClient.getEpochTime());
    } else if (rtcFound) {
        return rtc.now();
    } else {
        // Fallback básico se nada estiver disponível
        return DateTime(2023, 1, 1, 0, 0, 0);
    }
}

bool isSystemTimeReliable() {
    return systemTimeSynched || (rtcFound && rtcTimeReliable);
}

String formatDateTime(const DateTime& dt) {
    char buf[20];
    sprintf(buf, "%04d-%02d-%02d %02d:%02d:%02d",
            dt.year(), dt.month(), dt.day(),
            dt.hour(), dt.minute(), dt.second());
    return String(buf);
}

void initFileSystem() {
    if (!LittleFS.begin(true)) {
        Serial.println("[FS] Erro ao montar o sistema de ficheiros");
        return;
    }
    Serial.println("[FS] LittleFS montado com sucesso.");
}

void appendLogToFile(const String& message) {
    if (!ENABLE_FILE_LOGGING) return;

    File file = LittleFS.open(LOG_FILE, FILE_APPEND);
    if (!file) {
        Serial.println("[FS] Erro ao abrir ficheiro de log para escrita");
        return;
    }

    DateTime now = getCurrentTime();
    String timedMessage = "[" + formatDateTime(now) + "] " + message;

    if (file.println(timedMessage)) {
        // Serial.println("[FS] Log gravado.");
    } else {
        Serial.println("[FS] Erro ao gravar log.");
    }
    file.close();
}

void processScheduledIrrigation() {
    if (!isSystemTimeReliable()) return;

    DateTime now = getCurrentTime();

    // Reset scheduledCycleToday à meia-noite
    if (now.hour() == 0 && now.minute() == 0) {
        scheduledCycleToday = false;
    }

    // Verificar se é hora de iniciar o ciclo agendado
    if (!scheduledCycleToday && now.dayOfWeek() == SCHEDULE_DAY &&
        now.hour() == SCHEDULE_HOUR && now.minute() == SCHEDULE_MINUTE) {

        Serial.println("[SCHED] Iniciando ciclo de irrigacao agendado...");
        scheduledCycleToday = true;
        appendLogToFile("Iniciando ciclo agendado");
        manualStartFullCycle("SCHEDULED");
    }

    // Gerir sequencia de válvulas se um ciclo estiver a correr
    if (currentValveInSequence != -1) {
        if (millis() - currentValveStartTime >= valveActivationDurationMillis) {
            // Desligar válvula atual
            controlValve(currentValveInSequence + 1, false);

            // Passar para a próxima
            currentValveInSequence++;

            if (currentValveInSequence < NUM_VALVES) {
                // Ligar próxima válvula
                controlValve(currentValveInSequence + 1, true);
                currentValveStartTime = millis();
            } else {
                // Fim da sequência
                Serial.println("[SCHED] Ciclo de irrigacao concluido.");
                appendLogToFile("Ciclo concluido");
                currentValveInSequence = -1;
            }
        }
    }
}

void controlValve(int valveNumber, bool state) {
    if (valveNumber < 1 || valveNumber > NUM_VALVES) return;

    int pin = VALVE_PINS[valveNumber - 1];
    digitalWrite(pin, state ? RELAY_ON_STATE : RELAY_OFF_STATE);

    Serial.print("[VALVE] Valvula ");
    Serial.print(valveNumber);
    Serial.print(" alterada para ");
    Serial.println(state ? "LIGADA" : "DESLIGADA");

    sendStatusToAPI(valveNumber, state);
}

void sendStatusToAPI(int valveNumber, bool state) {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    String url = String(API_SERVER_HOST) + API_ENDPOINT_VALVE_STATUS;

    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Authorization", "Bearer " + String(API_TOKEN));

    StaticJsonDocument<200> doc;
    doc["valve_id"] = valveNumber;
    doc["state"] = state ? 1 : 0;
    doc["timestamp"] = timeClient.getEpochTime();

    String jsonResponse;
    serializeJson(doc, jsonResponse);

    int httpResponseCode = http.POST(jsonResponse);

    if (httpResponseCode > 0) {
        // Serial.println("[API] Estado enviado com sucesso.");
    } else {
        Serial.print("[API] Erro ao enviar estado: ");
        Serial.println(http.errorToString(httpResponseCode));
    }
    http.end();
}

void processIncomingCommands() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    String url = String(API_SERVER_HOST) + API_ENDPOINT_COMMANDS;

    http.begin(url);
    http.addHeader("Authorization", "Bearer " + String(API_TOKEN));

    int httpResponseCode = http.GET();

    if (httpResponseCode == 200) {
        String payload = http.getString();
        DynamicJsonDocument doc(1024);
        deserializeJson(doc, payload);

        JsonArray commands = doc["commands"].as<JsonArray>();
        for (JsonObject cmd : commands) {
            String action = cmd["action"];
            if (action == "toggle_valve") {
                manualValveControl(cmd["valve_id"], cmd["state"], "REMOTE_API");
            } else if (action == "start_cycle") {
                manualStartFullCycle("REMOTE_API");
            } else if (action == "stop_all") {
                manualStopAllValves("REMOTE_API");
            }
        }
    }
    http.end();
}

void manualValveControl(int valveNumber, bool state, String source) {
    controlValve(valveNumber, state);
    appendLogToFile("Manual control: Valve " + String(valveNumber) + " " + (state ? "ON" : "OFF") + " by " + source);
}

void manualStartFullCycle(String source) {
    if (currentValveInSequence != -1) return; // Ciclo já em curso

    currentValveInSequence = 0;
    controlValve(currentValveInSequence + 1, true);
    currentValveStartTime = millis();
    appendLogToFile("Full cycle started by " + source);
}

void manualStopAllValves(String source) {
    currentValveInSequence = -1;
    for (int i = 1; i <= NUM_VALVES; i++) {
        controlValve(i, false);
    }
    appendLogToFile("All valves stopped by " + source);
}
