// ==========================================================================
// ESP32 Irrigation Controller Firmware
// ==========================================================================

// --------------------------------------------------------------------------
// Includes
// --------------------------------------------------------------------------
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h> // Para JSON com a API

// Para NTP (Network Time Protocol)
#include <NTPClient.h>
#include <WiFiUdp.h>

// Para RTC (Real-Time Clock) - Exemplo com DS3231
#include <Wire.h> // Necessário para I2C comunicação com RTC
#include <RTClib.h>
RTC_DS3231 rtc; // Instancia o objeto RTC DS3231
bool rtcFound = false; // Flag para indicar se o RTC foi detectado
bool rtcTimeReliable = false; // Flag para indicar se a hora do RTC é confiável (ex: sincronizada com NTP)

// Para LoRa (se o hardware ESP32 tiver e for ser usado no futuro)
// #include <SPI.h>
// #include <LoRa.h>

// Para FileSystem (LittleFS)
#include <FS.h>
#include <LittleFS.h>
// Definições dos pinos LoRa (exemplo para Heltec WiFi LoRa 32)
// #define LORA_SCK 5
// #define LORA_MISO 19
// #define LORA_MOSI 27
// #define LORA_SS 18
// #define LORA_RST 14
// #define LORA_DIO0 26
// #define LORA_BAND 868E6 // Ou 915E6, 433E6 conforme a sua região/módulo

// --------------------------------------------------------------------------
// Definições e Configurações Globais
// --------------------------------------------------------------------------

// --- Configurações de Rede Wi-Fi ---
const char* WIFI_SSID = "SUA_REDE_WIFI";
const char* WIFI_PASSWORD = "SUA_SENHA_WIFI";

// --- Configurações do Servidor API Laravel ---
const char* API_SERVER_HOST = "http://seu_laravel_server.com"; // Ex: http://192.168.1.100:8000 ou URL de produção
const char* API_TOKEN = "SEU_TOKEN_API_SANCTUM_AQUI"; // Token para autenticação na API
const char* API_ENDPOINT_VALVE_STATUS = "/api/esp32/valve-status";
const char* API_ENDPOINT_LOG = "/api/esp32/log";
const char* API_ENDPOINT_CONFIG = "/api/esp32/config";

// --- Configurações NTP ---
const char* NTP_SERVER = "pool.ntp.org";
const long GMT_OFFSET_SEC = 0; // Offset do GMT em segundos (Ex: Portugal Continental é 0 ou 3600 no verão)
const int DAYLIGHT_OFFSET_SEC = 3600; // Offset do horário de verão (Ex: 3600 para +1 hora)
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, NTP_SERVER, GMT_OFFSET_SEC, DAYLIGHT_OFFSET_SEC);
// Nota: A gestão de horário de verão pode ser complexa. Pode ser mais simples obter UTC e tratar no servidor.

// --- Pinos das Válvulas (Relés) ---
// Mapear os 5 relés para os pinos GPIO do ESP32
const int VALVE_PINS[5] = {23, 22, 21, 19, 18}; // Exemplo de pinos, ajuste conforme a sua placa
const int NUM_VALVES = 5;
const int RELAY_ON_STATE = HIGH; // Mude para LOW se os seus relés forem ativos em baixo
const int RELAY_OFF_STATE = LOW;  // Mude para HIGH se os seus relés forem ativos em baixo


// --- Lógica de Agendamento ---
const int SCHEDULE_DAY = 5; // 0=Dom, 1=Seg, ..., 5=Sex, 6=Sab. Sexta-feira.
const int SCHEDULE_HOUR = 10; // 10 da manhã
const int SCHEDULE_MINUTE = 00; // 00 minutos
const int VALVE_DURATION_MINUTES = 5;
unsigned long valveActivationDurationMillis = VALVE_DURATION_MINUTES * 60 * 1000;

// --- Variáveis de Estado ---
int currentValveInSequence = -1; // -1 significa que nenhum ciclo de agendamento está ativo
unsigned long currentValveStartTime = 0;
bool systemTimeSynched = false; // NTP sincronizado
bool scheduledCycleToday = false; // Para garantir que o ciclo agendado só corre uma vez por dia agendado

// --- Configurações de Log em Ficheiro ---
#define LOG_FILE "/irrigation_log.txt"
#define MAX_LOG_SIZE (10 * 1024) // 10KB para o tamanho máximo do log (ajuste conforme necessário)


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

// void initLoRa(); // Se for usar LoRa

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

    // Inicializar e sincronizar tempo com NTP
    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("[SETUP] Iniciando cliente NTP...");
        timeClient.begin();
        syncTimeNTP(); // Primeira tentativa de sincronização
    } else {
        Serial.println("[SETUP] Wi-Fi nao conectado. NTP nao iniciado.");
    }

    // Inicializar RTC (se estiver a usar)
    initRTC(); // Tenta inicializar o RTC
    // A sincronização do RTC com NTP ocorrerá em syncTimeNTP se bem sucedida

    // Inicializar LoRa (se estiver a usar)
    // initLoRa();

    Serial.println("[SETUP] Configuracao inicial completa.");
    Serial.println("-----------------------------------------");
}

// ==========================================================================
// LOOP - Executado continuamente
// ==========================================================================
void loop() {
    // Manter Wi-Fi conectado
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[LOOP] Wi-Fi desconectado. Tentando reconectar...");
        connectWiFi();
        // Se reconectar, tentar sincronizar NTP novamente, pois pode ter perdido a hora
        if(WiFi.status() == WL_CONNECTED && !systemTimeSynched) {
            syncTimeNTP();
        }
    }

    // Atualizar o cliente NTP (necessário para obter a hora atualizada)
    // E tentar sincronizar se ainda não estiver sincronizado
    if (WiFi.status() == WL_CONNECTED) { // Só faz sentido tentar NTP com WiFi
        if (systemTimeSynched) {
            timeClient.update(); // Esta chamada deve ser regular para manter a precisão
        } else {
            // Tentar sincronizar o tempo periodicamente se falhou no setup ou após desconexão
            static unsigned long lastNtpTry = 0;
            if (millis() - lastNtpTry > 60000) { // Tentar a cada minuto
                syncTimeNTP(); // Tentará NTP e, se bem sucedido, sincronizará RTC
                lastNtpTry = millis();
            }
        }
    }

    // Verificar e processar comandos manuais recebidos
    processIncomingCommands();

    // Lógica de irrigação agendada (pode ser afetada/interrompida por comandos manuais)
    if (isSystemTimeReliable()) {
        processScheduledIrrigation();
    } else {
        Serial.println("[LOOP] Hora do sistema nao confiavel. Agendamento suspenso.");
    }

    // Outras tarefas no loop:
    // - Enviar dados de sensores (se houver)
    // - Manter comunicação LoRa (se ativa)

    delay(1000); // Delay principal do loop para não sobrecarregar
}

// --------------------------------------------------------------------------
// Implementação das Funções Auxiliares
// --------------------------------------------------------------------------

/**
 * Conecta à rede Wi-Fi.
 */
void connectWiFi() {
    if (WiFi.status() == WL_CONNECTED) {
        return;
    }
    Serial.print("[WiFi] Conectando a ");
    Serial.println(WIFI_SSID);

    WiFi.mode(WIFI_STA); // Definir modo Station explicitamente
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

    int attempts = 0;
    while (WiFi.status() != WL_CONNECTED && attempts < 20) { // Tentar por aprox. 10 segundos
        delay(500);
        Serial.print(".");
        attempts++;
    }

    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\n[WiFi] Conectado!");
        Serial.print("[WiFi] Endereco IP: ");
        Serial.println(WiFi.localIP());
    } else {
        Serial.println("\n[WiFi] Falha ao conectar.");
    }
}

/**
 * Sincroniza o tempo usando NTP.
 */
void syncTimeNTP() {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[NTP] Wi-Fi nao conectado. Impossivel sincronizar NTP.");
        systemTimeSynched = false;
        return;
    }

    Serial.println("[NTP] Tentando sincronizar hora...");
    if (timeClient.forceUpdate()) { // forceUpdate pode bloquear por alguns segundos
        systemTimeSynched = true;
        Serial.print("[NTP] Hora sincronizada: ");
        Serial.println(timeClient.getFormattedTime());
        // Após sincronizar com NTP, poderia atualizar o RTC se estiver a usar um
        // if (rtcFound) { rtc.adjust(DateTime(timeClient.getEpochTime())); }
        scheduledCycleToday = false; // Resetar flag do ciclo diário após sincronização bem sucedida
    } else {
        systemTimeSynched = false;
        Serial.println("[NTP] Falha ao sincronizar hora.");
    }
}

/*
// Exemplo de inicialização do RTC
void initRTC() {
    #if defined(rtc) // Apenas se a variável rtc (ex: RTC_DS3231 rtc;) estiver definida
    Serial.println("[RTC] Procurando modulo RTC...");
    if (!rtc.begin()) {
        Serial.println("[RTC] Modulo RTC nao encontrado!");
        rtcFound = false;
    } else {
        Serial.println("[RTC] Modulo RTC encontrado.");
        rtcFound = true;
        if (rtc.lostPower()) {
            Serial.println("[RTC] Energia perdida, definindo hora padrao!");
            // Define uma hora padrão ou tenta NTP imediatamente.
            // Ex: rtc.adjust(DateTime(F(__DATE__), F(__TIME__))); // Hora da compilação
        }
    }
    #endif
}
*/

/*
// Exemplo de como obter a hora (priorizando NTP, fallback para RTC)
String getFormattedTime() {
    if (systemTimeSynched) {
        return timeClient.getFormattedTime();
    } else if (rtcFound) {
        // DateTime now = rtc.now();
        // char buf[] = "YYYY-MM-DD hh:mm:ss";
        // return now.toString(buf);
        return "[RTC] " + String(rtc.now().timestamp()); // Exemplo, adaptar formatação
    }
    return "Hora nao disponivel";
}
*/

/**
 * Controla uma válvula específica.
 * @param valveNumber O número da válvula (0 a NUM_VALVES-1).
 * @param state true para ligar (HIGH), false para desligar (LOW).
 */
void controlValve(int valveNumber, bool state) {
    if (valveNumber < 0 || valveNumber >= NUM_VALVES) {
        Serial.print("[VALVE] Numero de valvula invalido: ");
        Serial.println(valveNumber);
        return;
    }

    Serial.print("[VALVE] Valvula ");
    Serial.print(valveNumber + 1); // Mostrar como 1-5 para o utilizador
    Serial.print(" -> ");
    Serial.println(state ? "LIGANDO" : "DESLIGANDO");

    digitalWrite(VALVE_PINS[valveNumber], state ? RELAY_ON_STATE : RELAY_OFF_STATE);
    // Aqui poderia enviar o estado para a API
    // (valveNumber é 0-indexed, API pode esperar 1-indexed)
    sendStatusToAPI(valveNumber + 1, state);
}

/**
 * Processa a lógica de irrigação agendada.
 */
void processScheduledIrrigation() {
    if (!systemTimeSynched) return; // Requer hora sincronizada

    int currentDay = timeClient.getDay(); // Domingo = 0, ..., Sábado = 6
    int currentHour = timeClient.getHours();
    int currentMinute = timeClient.getMinutes();

    // Verificar se é dia e hora do agendamento principal
    if (currentDay == SCHEDULE_DAY && currentHour == SCHEDULE_HOUR && currentMinute == SCHEDULE_MINUTE) {
        if (!scheduledCycleToday && currentValveInSequence == -1) { // Iniciar apenas se não correu hoje e nenhum ciclo está ativo
            Serial.println("[SCHEDULE] Hora do agendamento! Iniciando ciclo de irrigacao...");
            currentValveInSequence = 0; // Começar com a primeira válvula (índice 0)
            controlValve(currentValveInSequence, true);
            currentValveStartTime = millis();
            scheduledCycleToday = true; // Marcar que o ciclo agendado para hoje já começou
            // Enviar log para API sobre início do ciclo
            JsonDocument detailsDoc; // ArduinoJson v7+
            detailsDoc["schedule_day"] = SCHEDULE_DAY;
            detailsDoc["schedule_time"] = String(SCHEDULE_HOUR) + ":" + String(SCHEDULE_MINUTE);
            sendLogToAPI("INFO", "Ciclo de irrigacao agendado iniciado.", detailsDoc.as<JsonObjectConst>());
        }
    } else {
        // Se não for a hora exata do agendamento, resetar a flag `scheduledCycleToday`
        // para permitir que o ciclo corra no próximo dia agendado.
        // Isto é importante para o caso de o ESP32 reiniciar noutro dia.
        if (currentDay != SCHEDULE_DAY || currentHour != SCHEDULE_HOUR) { // Se já passou do dia/hora do agendamento
             scheduledCycleToday = false;
        }
    }

    // Gerir a sequência de válvulas se um ciclo estiver ativo
    if (currentValveInSequence != -1) {
        unsigned long elapsedTime = millis() - currentValveStartTime;
        if (elapsedTime >= valveActivationDurationMillis) {
            Serial.print("[SCHEDULE] Tempo da valvula ");
            Serial.print(currentValveInSequence + 1);
            Serial.println(" esgotado.");

            controlValve(currentValveInSequence, false); // Desligar válvula atual

            currentValveInSequence++; // Mover para a próxima válvula

            if (currentValveInSequence < NUM_VALVES) {
                Serial.print("[SCHEDULE] Ativando proxima valvula: ");
                Serial.println(currentValveInSequence + 1);
                controlValve(currentValveInSequence, true);
                currentValveStartTime = millis(); // Resetar tempo para a nova válvula
            } else {
                Serial.println("[SCHEDULE] Ciclo de irrigacao concluido.");
                currentValveInSequence = -1; // Terminar o ciclo
                 // Enviar log para API sobre fim do ciclo
                JsonDocument detailsDoc; // ArduinoJson v7+
                // Adicionar detalhes relevantes se necessário, ex: duração total, etc.
                detailsDoc["status"] = "Completed successfully";
                sendLogToAPI("INFO", "Ciclo de irrigacao agendado concluido.", detailsDoc.as<JsonObjectConst>());
            }
        }
    }
}


/**
 * Envia um log para a API Laravel.
 * @param level Nível do log (ex: "INFO", "ERROR", "WARNING").
 * @param message A mensagem principal do log.
 * @param details Objeto JSON (constante) com detalhes adicionais. Pode ser um JsonObjectConst vazio se não houver detalhes.
 */
void sendLogToAPI(String level, String message, JsonObjectConst details) {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[API] WiFi desconectado. Log nao enviado: " + message);
        return;
    }

    HTTPClient http;
    String serverPath = String(API_SERVER_HOST) + String(API_ENDPOINT_LOG);

    http.begin(serverPath); // Pode precisar especificar o certificado raiz para HTTPS em produção
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Authorization", "Bearer " + String(API_TOKEN));
    http.addHeader("Accept", "application/json");

    JsonDocument doc; // ArduinoJson v7+ para construir o payload
    doc["level"] = level;
    doc["message"] = message;
    if (details && !details.isNull()) { // Copia os detalhes se existirem
        doc["details"] = details;
    }
    doc["source"] = "ESP32"; // Identifica a origem do log

    String requestBody;
    serializeJson(doc, requestBody);

    Serial.print("[API] Enviando Log para " + serverPath + ": ");
    Serial.println(requestBody);

    int httpResponseCode = http.POST(requestBody);

    if (httpResponseCode > 0) {
        Serial.print("[API] Resposta do Log: ");
        Serial.println(httpResponseCode);
        if (httpResponseCode == HTTP_CODE_OK || httpResponseCode == HTTP_CODE_CREATED) {
            // String responsePayload = http.getString();
            // Serial.println("[API] Payload da Resposta: " + responsePayload);
             // Sucesso
        } else {
            Serial.print("[API] Erro no servidor: ");
            Serial.println(http.getString());
        }
    } else {
        Serial.print("[API] Falha na conexao ou envio do Log. Codigo de erro HTTPClient: ");
        Serial.println(httpResponseCode); // ex: -1 para falha de conexão
        // Serial.println(http.errorToString(httpResponseCode).c_str()); // Para erros do HTTPClient
    }
    http.end();
}

/**
 * Envia o estado de uma válvula para a API Laravel.
 * @param valveNumber O número da válvula (1-indexed).
 * @param state O estado da válvula (true para LIGADA, false para DESLIGADA).
 */
void sendStatusToAPI(int valveNumber, bool state) {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[API] WiFi desconectado. Estado da valvula " + String(valveNumber) + " nao enviado.");
        return;
    }

    HTTPClient http;
    String serverPath = String(API_SERVER_HOST) + String(API_ENDPOINT_VALVE_STATUS);

    http.begin(serverPath);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Authorization", "Bearer " + String(API_TOKEN));
    http.addHeader("Accept", "application/json");

    JsonDocument doc; // ArduinoJson v7+
    doc["valve_number"] = valveNumber;
    doc["state"] = state; // true para ON, false para OFF
    // Poderia adicionar um timestamp do ESP32 se o servidor precisar
    // doc["timestamp_device"] = timeClient.getEpochTime();

    String requestBody;
    serializeJson(doc, requestBody);

    Serial.print("[API] Enviando Estado da Valvula para " + serverPath + ": ");
    Serial.println(requestBody);

    int httpResponseCode = http.POST(requestBody);

    if (httpResponseCode > 0) {
        Serial.print("[API] Resposta do Estado da Valvula: ");
        Serial.println(httpResponseCode);
         if (httpResponseCode != HTTP_CODE_OK && httpResponseCode != HTTP_CODE_CREATED) {
            Serial.print("[API] Erro no servidor: ");
            Serial.println(http.getString());
        }
    } else {
        Serial.print("[API] Falha na conexao ou envio do Estado da Valvula. Codigo de erro HTTPClient: ");
        Serial.println(httpResponseCode);
    }
    http.end();
}


// NOTA: As funções para RTC, LoRa e comunicação API mais detalhada (obter config, etc.)
// precisam ser implementadas conforme necessário. Este é um esqueleto inicial.
// A gestão de erros na comunicação HTTP também precisa ser mais robusta.
// A desserialização de JSON da resposta da API também seria necessária para obter configurações.
// Para ArduinoJson v6, a sintaxe é um pouco diferente (DynamicJsonDocument).
// Este código assume ArduinoJson v7 ou mais recente para JsonDocument e JsonObject.
// Se usar v6, ajuste: JsonDocument doc; -> DynamicJsonDocument doc(1024);
// e doc.as<JsonObject>() -> doc.as<JsonObjectConst>() ou apenas doc dependendo do contexto.
// Ou, para criar um objeto: JsonObject obj = doc.to<JsonObject>();
// E para adicionar: obj["key"] = "value";
// Para aninhar: JsonObject nested = obj.createNestedObject("nested_key"); nested["sub_key"] = "sub_value";
// E para passar como JsonObject: sendLogToAPI("INFO", "Msg", doc.as<JsonObject>()); -> sendLogToAPI("INFO", "Msg", obj);
// Se for JsonObjectConst: sendLogToAPI("INFO", "Msg", doc.as<JsonObjectConst>());
// Geralmente, para construir JSON, usa-se a versão mutável.
// Verifique a documentação do ArduinoJson para a versão que está a usar.
// Para ArduinoJson 7:
// JsonDocument doc; // Para criar
// JsonObject obj = doc.to<JsonObject>(); // Para adicionar membros a um objeto
// JsonArray arr = doc.to<JsonArray>(); // Para adicionar elementos a um array
// serializeJson(doc, Serial); // Para imprimir
// deserializeJson(doc, http.getStream()); // Para ler da stream HTTP
// JsonObjectConst root = doc.as<JsonObjectConst>(); // Para ler um JSON recebido
// const char* value = root["key"];
// Para passar um objeto construído: sendLogToAPI("INFO", "Msg", doc.as<JsonObject>());
// Se a função sendLogToAPI espera um JsonObject para detalhes, e você está a construir
// um novo JsonDocument dentro dela para o corpo do request, então não precisa passar JsonObject.
// Basta passar os dados brutos e construir o JsonDocument final na função de envio.
// Revisando sendLogToAPI:
// void sendLogToAPI(String level, String message, String jsonDetailsString) // Passar string JSON
// ou
// void sendLogToAPI(String level, String message, std::function<void(JsonObject&)> buildDetails) // Usar callback para construir detalhes
// A forma atual com JsonObject details como parâmetro pode ser um pouco confusa com escopo de JsonDocument.
// Para simplificar, a função sendLogToAPI pode receber os dados e montar o JSON internamente.
// Ex: void sendLogToAPI(String level, String message, String optionalDetailKey = "", String optionalDetailValue = "")
// E dentro dela:
// JsonDocument doc;
// doc["level"] = level;
// ...
// if (optionalDetailKey != "") { JsonObject details = doc.createNestedObject("details"); details[optionalDetailKey] = optionalDetailValue; }
// Esta abordagem é mais simples para logs básicos. Para detalhes complexos, uma string JSON ou um callback é melhor.
// No exemplo atual, `doc.as<JsonObject>()` na chamada de `sendLogToAPI` implicaria que `doc` já está preenchido.
// Se `sendLogToAPI` cria o seu próprio `JsonDocument` para o POST, então não precisa de `JsonObject details` como parâmetro,
// mas sim dos dados para colocar nos detalhes.
// Ex:
// sendLogToAPI("INFO", "Ciclo Iniciado", "status", "OK");
// void sendLogToAPI(String level, String message, String detailKey = "", String detailValue = "") {
//    JsonDocument postDoc;
//    postDoc["level"] = level;
//    postDoc["message"] = message;
//    if (detailKey != "") {
//        JsonObject detailsObj = postDoc.createNestedObject("details");
//        detailsObj[detailKey] = detailValue;
//    }
//    ... POST postDoc ...
// }
// Para o código fornecido, a intenção parece ser:
// JsonDocument detailsDoc; detailsDoc["algum_dado"] = "valor";
// sendLogToAPI("INFO", "Mensagem", detailsDoc.as<JsonObject>());
// E em sendLogToAPI:
// JsonDocument postRequestDoc; ... postRequestDoc["details"] = details; // Copia o objeto
// Isto é suportado pelo ArduinoJson.


/**
 * Inicializa o sistema de ficheiros LittleFS.
 */
void initFileSystem() {
    // O parâmetro true para begin() formata o sistema de ficheiros se não puder ser montado.
    // Use false se não quiser formatação automática para evitar perda de dados.
    if (!LittleFS.begin(true)) {
        Serial.println("[FS] Falha ao montar LittleFS! Os logs em ficheiro nao funcionarao.");
        return;
    }
    Serial.println("[FS] LittleFS montado com sucesso.");

    // Opcional: Listar ficheiros para debug
    // File root = LittleFS.open("/");
    // File file = root.openNextFile();
    // Serial.println("[FS] Conteudo da raiz:");
    // while(file){
    //     Serial.print("  FILE: "); Serial.print(file.name());
    //     Serial.print("\tSIZE: "); Serial.println(file.size());
    //     file = root.openNextFile();
    // }
    // if(root) root.close();
    // if(file) file.close();

    File logFile = LittleFS.open(LOG_FILE, "r");
    if (logFile) {
        Serial.print("[FS] Tamanho atual do ficheiro de log '");
        Serial.print(LOG_FILE); Serial.print("': "); Serial.println(logFile.size());
        logFile.close();
    } else {
        Serial.print("[FS] Ficheiro de log '");
        Serial.print(LOG_FILE); Serial.println("' nao encontrado. Sera criado no primeiro log.");
    }
}

/**
 * Adiciona uma mensagem ao ficheiro de log no LittleFS.
 * Inclui timestamp se a hora do sistema for confiável.
 * Implementa uma rotação simples de log.
 */
void appendLogToFile(const String& message) {
    if (!LittleFS.begin()) {
        Serial.println("[FS] LittleFS nao montado. Impossivel escrever no log: " + message);
        return;
    }

    String timedMessage = "";
    DateTime now = getCurrentTime();
    if (isSystemTimeReliable() && now.year() >= 2023) {
        timedMessage = formatDateTime(now) + " - " + message;
    } else {
        if (rtcFound) { // Se RTC existe, usa a sua hora mesmo que não sincronizada, mas avisa
            timedMessage = "[RTC_UNSYNCED] " + formatDateTime(rtc.now()) + " - " + message;
        } else { // Sem RTC e sem NTP
            timedMessage = "[NO_TIME] (" + String(millis()/1000) + "s since boot) - " + message;
        }
    }

    File logFile = LittleFS.open(LOG_FILE, "a");
    if (!logFile) {
        Serial.println("[FS] Falha ao abrir/criar ficheiro de log para append: " + String(LOG_FILE));
        return;
    }

    if (!logFile.println(timedMessage)) {
        Serial.println("[FS] Erro ao escrever no ficheiro de log.");
    }
    // Serial.println("[FS_LOG] " + timedMessage); // Para debug do log em ficheiro via Serial

    unsigned long currentSize = logFile.size();
    logFile.close();

    if (currentSize > MAX_LOG_SIZE) {
        Serial.print("[FS] Ficheiro de log '"); Serial.print(LOG_FILE);
        Serial.print("' atingiu o tamanho maximo ("); Serial.print(currentSize);
        Serial.println(" bytes). A tentar rotacionar...");

        String oldLogFile = String(LOG_FILE) + ".old";

        if (LittleFS.exists(oldLogFile)) {
            LittleFS.remove(oldLogFile);
        }

        if (LittleFS.rename(LOG_FILE, oldLogFile)) {
            Serial.println("[FS] Log rotacionado. Antigo log: " + oldLogFile);
        } else {
            Serial.println("[FS] Falha ao rotacionar o log.");
        }
    }
}

// Comentários sobre dependências de bibliotecas no platformio.ini:
// lib_deps =
//   arduino-libraries/NTPClient
//   adafruit/RTClib @ ^2.1.1
//   bblanchon/ArduinoJson @ ^7.0.3  ; (ou versão compatível mais recente)
//   sandeepmistry/LoRa             ; (se for usar LoRa)
//   FS                             ; (Parte do ESP32 core)
//   LittleFS                       ; (Para ESP32, geralmente LittleFS_esp32 ou parte do core)
// Verifique a documentação do seu ESP32 core/PlatformIO para LittleFS.
// Para o ESP32 Arduino Core v2.x.x, LittleFS é geralmente incluído.
// Para versões mais antigas, pode ser necessário: ESP32 Sketch Data Upload tool e uma lib como `lorol/LITTLEFS_esp32`.
// Assumindo core recente onde LittleFS.h é suficiente.

// --------------------------------------------------------------------------
// Implementação das Funções de Comando Manual (Placeholders)
// --------------------------------------------------------------------------

/**
 * Placeholder para processar comandos recebidos (ex: via Serial, MQTT, HTTP Polling).
 * Esta função seria chamada no loop principal.
 */
void processIncomingCommands() {
    // Exemplo: Ler do Serial para teste
    // if (Serial.available() > 0) {
    //     String command = Serial.readStringUntil('\n');
    //     command.trim();
    //     Serial.print("[CMD] Comando recebido via Serial: ");
    //     Serial.println(command);

    //     if (command.startsWith("VALVE_ON_")) {
    //         int valveNum = command.substring(9).toInt();
    //         if (valveNum > 0 && valveNum <= NUM_VALVES) {
    //             manualValveControl(valveNum - 1, true, "SERIAL");
    //         }
    //     } else if (command.startsWith("VALVE_OFF_")) {
    //         int valveNum = command.substring(10).toInt();
    //         if (valveNum > 0 && valveNum <= NUM_VALVES) {
    //             manualValveControl(valveNum - 1, false, "SERIAL");
    //         }
    //     } else if (command == "START_CYCLE") {
    //         manualStartFullCycle("SERIAL");
    //     } else if (command == "STOP_ALL") {
    //         manualStopAllValves("SERIAL");
    //     } else {
    //         Serial.println("[CMD] Comando serial desconhecido.");
    //     }
    // }

    // Aqui seria o local para verificar:
    // - Mensagens MQTT subscritas
    // - Resposta de um pedido HTTP a /api/esp32/commands (polling)
    // - Estado de botões físicos
}

/**
 * Controla uma válvula manualmente.
 * Pode precisar interromper o ciclo agendado ou coexistir dependendo das regras.
 * @param valveNumber Número da válvula (0-indexed).
 * @param state true para ligar, false para desligar.
 * @param source Origem do comando (ex: "API", "TELEGRAM", "SERIAL", "BUTTON").
 */
void manualValveControl(int valveNumber, bool state, String source) {
    Serial.print("[MANUAL_CMD] Comando manual recebido de '");
    Serial.print(source);
    Serial.print("': Válvula ");
    Serial.print(valveNumber + 1);
    Serial.print(state ? " ON" : " OFF");
    Serial.println();

    // Log para API
    JsonDocument detailsDoc;
    detailsDoc["valve_number"] = valveNumber + 1;
    detailsDoc["requested_state"] = state ? "ON" : "OFF";
    detailsDoc["source"] = source;
    sendLogToAPI("INFO", "Comando manual para valvula recebido.", detailsDoc.as<JsonObjectConst>());

    // Lógica de interrupção do ciclo agendado:
    // Se uma válvula é controlada manualmente, o ciclo agendado principal (currentValveInSequence)
    // deve ser interrompido para evitar conflitos ou comportamento inesperado.
    if (currentValveInSequence != -1) {
        Serial.println("[MANUAL_CMD] Ciclo agendado estava ativo. Interrompendo ciclo agendado.");
        // Desligar a válvula que estava ativa no ciclo agendado, se houver e for diferente da manual
        if (VALVE_PINS[currentValveInSequence] != VALVE_PINS[valveNumber] || !state) { // se for a mesma valvula e o comando for para ligar, não precisa desligar
             controlValve(currentValveInSequence, false); // Desliga a válvula do ciclo
        }
        currentValveInSequence = -1; // Marca o ciclo agendado como inativo
        JsonDocument cycleDetailsDoc;
        cycleDetailsDoc["reason"] = "Comando manual recebido para valvula " + String(valveNumber + 1);
        cycleDetailsDoc["source"] = source;
        sendLogToAPI("INFO", "Ciclo de irrigacao agendado interrompido por comando manual.", cycleDetailsDoc.as<JsonObjectConst>());
    }

    // Executar o comando manual na válvula
    controlValve(valveNumber, state);
    // Nota: Se o comando for para ligar uma válvula, não há temporizador automático aqui.
    // A válvula permanecerá ligada até um comando manual para desligar, ou até o sistema reiniciar,
    // ou se uma nova lógica de ciclo manual for iniciada.
    // Para operações manuais temporizadas, esta função precisaria de mais lógica.
}

/**
 * Inicia um ciclo de irrigação completo manualmente.
 * @param source Origem do comando.
 */
void manualStartFullCycle(String source) {
    Serial.print("[MANUAL_CMD] Comando manual recebido de '");
    Serial.print(source);
    Serial.println("': Iniciar Ciclo Completo.");

    JsonDocument detailsDoc;
    detailsDoc["source"] = source;
    sendLogToAPI("INFO", "Comando manual para iniciar ciclo completo recebido.", detailsDoc.as<JsonObjectConst>());

    if (currentValveInSequence != -1) {
        Serial.println("[MANUAL_CMD] Um ciclo ja esta ativo. Parando ciclo atual antes de iniciar novo ciclo manual.");
        controlValve(currentValveInSequence, false); // Desliga a válvula do ciclo atual
    }

    Serial.println("[MANUAL_CMD] Iniciando ciclo de irrigacao manual...");
    currentValveInSequence = 0; // Começar com a primeira válvula
    controlValve(currentValveInSequence, true);
    currentValveStartTime = millis(); // Usar as mesmas variáveis do ciclo agendado
    scheduledCycleToday = true; // Marcar como se o ciclo tivesse corrido para evitar conflito com agendamento no mesmo dia
                                // Ou usar uma flag separada `manualCycleActive = true;`

    // A lógica em `processScheduledIrrigation` agora também tratará este ciclo manual
    // porque `currentValveInSequence` não é -1.
}

/**
 * Para todas as válvulas e interrompe qualquer ciclo em andamento.
 * @param source Origem do comando.
 */
void manualStopAllValves(String source) {
    Serial.print("[MANUAL_CMD] Comando manual recebido de '");
    Serial.print(source);
    Serial.println("': Parar Todas as Valvulas / Ciclo.");

    JsonDocument detailsDoc;
    detailsDoc["source"] = source;
    sendLogToAPI("INFO", "Comando manual para PARAR TUDO recebido.", detailsDoc.as<JsonObjectConst>());

    if (currentValveInSequence != -1) {
        Serial.println("[MANUAL_CMD] Parando valvula ativa do ciclo: " + String(currentValveInSequence + 1));
        controlValve(currentValveInSequence, false);
    } else {
        Serial.println("[MANUAL_CMD] Nenhum ciclo ativo, mas desligando todas as valvulas por seguranca.");
    }

    currentValveInSequence = -1; // Interrompe qualquer ciclo (agendado ou manual)
    // scheduledCycleToday = false; // Permitir que um novo ciclo agendado comece se for a hora? Ou manter true?
                                 // Se parar tudo, talvez seja melhor manter true para evitar recomeço automático imediato.

    // Garantir que todas as válvulas estão desligadas, mesmo que não estivessem num ciclo
    for (int i = 0; i < NUM_VALVES; i++) {
        if (digitalRead(VALVE_PINS[i]) == RELAY_ON_STATE) { // Se alguma válvula estiver ligada fora do ciclo
            controlValve(i, false);
        }
    }
    Serial.println("[MANUAL_CMD] Todas as valvulas foram desligadas.");
}
