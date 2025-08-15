/*
 * ESP32 IOTCNT Controller v2.0
 * Sistema de Controlo de Condensadores para CNT
 * Integração completa com sistema web IOTCNT
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <NTPClient.h>
#include <WiFiUdp.h>
#include <LittleFS.h>

// Configurações WiFi
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Configurações do servidor IOTCNT
const char* serverURL = "http://192.168.1.100:8080/esp32-integration.php";
const char* apiKey = "iotcnt-esp32-key-2025";

// Configurações NTP
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", 0, 60000);

// Informações do dispositivo
String deviceMAC;
String deviceIP;
String firmwareVersion = "2.0.0";
String deviceName = "ESP32-IOTCNT-001";

// Pinos das válvulas (relés)
const int valvePins[] = {2, 4, 5, 18, 19};
const int numValves = 5;

// Pinos dos sensores
const int tempSensorPin = 34;
const int pressureSensorPin = 35;
const int statusLED = 2;

// Variáveis de estado
bool valveStates[numValves] = {false};
float lastTemperature = 0.0;
float lastPressure = 0.0;
unsigned long lastHeartbeat = 0;
unsigned long lastSensorRead = 0;
unsigned long lastCommandCheck = 0;

// Intervalos de tempo
const unsigned long heartbeatInterval = 60000;    // 1 minuto
const unsigned long sensorInterval = 30000;       // 30 segundos
const unsigned long commandInterval = 10000;      // 10 segundos

// Estado de conexão
bool wifiConnected = false;
bool serverConnected = false;
int reconnectAttempts = 0;
const int maxReconnectAttempts = 5;

void setup() {
  Serial.begin(115200);
  Serial.println("=== ESP32 IOTCNT Controller v2.0 ===");

  // Inicializar LittleFS
  if (!LittleFS.begin()) {
    Serial.println("Erro ao inicializar LittleFS");
  }

  // Configurar pinos
  setupPins();

  // Obter MAC address
  deviceMAC = WiFi.macAddress();
  Serial.println("MAC Address: " + deviceMAC);

  // Conectar WiFi
  connectWiFi();

  // Inicializar NTP
  timeClient.begin();

  // Registar dispositivo no servidor
  registerDevice();

  Serial.println("Sistema inicializado com sucesso!");
  blinkStatusLED(3, 200); // 3 piscadas rápidas
}

void loop() {
  unsigned long currentTime = millis();

  // Verificar conexão WiFi
  if (WiFi.status() != WL_CONNECTED) {
    wifiConnected = false;
    reconnectWiFi();
  } else {
    wifiConnected = true;
    deviceIP = WiFi.localIP().toString();
  }

  // Actualizar tempo NTP
  timeClient.update();

  // Ler sensores periodicamente
  if (currentTime - lastSensorRead >= sensorInterval) {
    readSensors();
    lastSensorRead = currentTime;
  }

  // Enviar heartbeat periodicamente
  if (currentTime - lastHeartbeat >= heartbeatInterval) {
    sendHeartbeat();
    lastHeartbeat = currentTime;
  }

  // Verificar comandos pendentes
  if (currentTime - lastCommandCheck >= commandInterval) {
    checkPendingCommands();
    lastCommandCheck = currentTime;
  }

  // Actualizar LED de estado
  updateStatusLED();

  delay(1000); // Delay principal
}

void setupPins() {
  // Configurar pinos das válvulas como saída
  for (int i = 0; i < numValves; i++) {
    pinMode(valvePins[i], OUTPUT);
    digitalWrite(valvePins[i], LOW);
    valveStates[i] = false;
  }

  // Configurar LED de estado
  pinMode(statusLED, OUTPUT);

  // Configurar pinos dos sensores como entrada
  pinMode(tempSensorPin, INPUT);
  pinMode(pressureSensorPin, INPUT);

  Serial.println("Pinos configurados");
}

void connectWiFi() {
  Serial.print("Conectando ao WiFi");
  WiFi.begin(ssid, password);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(500);
    Serial.print(".");
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    wifiConnected = true;
    deviceIP = WiFi.localIP().toString();
    Serial.println();
    Serial.println("WiFi conectado!");
    Serial.println("IP: " + deviceIP);
  } else {
    wifiConnected = false;
    Serial.println();
    Serial.println("Falha na conexão WiFi");
  }
}

void reconnectWiFi() {
  if (reconnectAttempts < maxReconnectAttempts) {
    Serial.println("Tentando reconectar WiFi...");
    WiFi.disconnect();
    delay(1000);
    WiFi.begin(ssid, password);
    reconnectAttempts++;
  }
}

void registerDevice() {
  if (!wifiConnected) return;

  HTTPClient http;
  http.begin(serverURL);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Authorization", "Bearer " + String(apiKey));

  // Criar JSON de registo
  DynamicJsonDocument doc(1024);
  doc["mac_address"] = deviceMAC;
  doc["ip_address"] = deviceIP;
  doc["firmware_version"] = firmwareVersion;
  doc["device_name"] = deviceName;

  String jsonString;
  serializeJson(doc, jsonString);

  int httpResponseCode = http.POST(jsonString);

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println("Dispositivo registado: " + response);
    serverConnected = true;
    reconnectAttempts = 0;
  } else {
    Serial.println("Erro no registo: " + String(httpResponseCode));
    serverConnected = false;
  }

  http.end();
}

void readSensors() {
  // Ler temperatura (simulada)
  int tempReading = analogRead(tempSensorPin);
  lastTemperature = map(tempReading, 0, 4095, 15, 25) + random(-20, 20) / 10.0;

  // Ler pressão (simulada)
  int pressureReading = analogRead(pressureSensorPin);
  lastPressure = map(pressureReading, 0, 4095, 180, 250) / 100.0 + random(-10, 10) / 100.0;

  Serial.println("Sensores - Temp: " + String(lastTemperature) + "°C, Pressão: " + String(lastPressure) + " bar");
}

void sendHeartbeat() {
  if (!wifiConnected) return;

  HTTPClient http;
  http.begin(String(serverURL) + "?action=heartbeat");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Authorization", "Bearer " + String(apiKey));

  // Criar JSON de heartbeat
  DynamicJsonDocument doc(2048);
  doc["mac_address"] = deviceMAC;
  doc["ip_address"] = deviceIP;
  doc["firmware_version"] = firmwareVersion;
  doc["uptime"] = millis();
  doc["free_heap"] = ESP.getFreeHeap();
  doc["wifi_rssi"] = WiFi.RSSI();

  // Adicionar dados dos sensores
  JsonArray sensorData = doc.createNestedArray("sensor_data");

  JsonObject tempSensor = sensorData.createNestedObject();
  tempSensor["type"] = "temperature";
  tempSensor["value"] = lastTemperature;
  tempSensor["unit"] = "°C";

  JsonObject pressureSensor = sensorData.createNestedObject();
  pressureSensor["type"] = "pressure";
  pressureSensor["value"] = lastPressure;
  pressureSensor["unit"] = "bar";

  // Adicionar estado das válvulas
  JsonArray valves = doc.createNestedArray("valves");
  for (int i = 0; i < numValves; i++) {
    JsonObject valve = valves.createNestedObject();
    valve["id"] = i + 1;
    valve["status"] = valveStates[i] ? "open" : "closed";
    valve["temperature"] = lastTemperature + random(-5, 5) / 10.0;
    valve["pressure"] = lastPressure + random(-20, 20) / 100.0;
  }

  String jsonString;
  serializeJson(doc, jsonString);

  int httpResponseCode = http.POST(jsonString);

  if (httpResponseCode > 0) {
    String response = http.getString();

    // Processar resposta para comandos pendentes
    DynamicJsonDocument responseDoc(2048);
    deserializeJson(responseDoc, response);

    if (responseDoc["status"] == "success" && responseDoc.containsKey("commands")) {
      JsonArray commands = responseDoc["commands"];
      for (JsonObject command : commands) {
        processCommand(command);
      }
    }

    serverConnected = true;
    Serial.println("Heartbeat enviado com sucesso");
  } else {
    Serial.println("Erro no heartbeat: " + String(httpResponseCode));
    serverConnected = false;
  }

  http.end();
}

void checkPendingCommands() {
  // Esta função é chamada periodicamente para verificar comandos
  // Os comandos são recebidos principalmente via heartbeat
  Serial.println("Verificando comandos pendentes...");
}

void processCommand(JsonObject command) {
  int commandId = command["id"];
  String commandType = command["command"];
  JsonObject parameters = command["parameters"];

  Serial.println("Processando comando: " + commandType + " (ID: " + String(commandId) + ")");

  bool success = false;
  String result = "";

  if (commandType == "ping") {
    success = true;
    result = "pong";

  } else if (commandType == "status") {
    success = true;
    result = "online";

  } else if (commandType == "restart") {
    success = true;
    result = "restarting";
    sendCommandResult(commandId, success, result);
    delay(1000);
    ESP.restart();

  } else if (commandType == "valve_open") {
    int valveId = parameters["valve_id"] | 1;
    if (valveId >= 1 && valveId <= numValves) {
      openValve(valveId - 1);
      success = true;
      result = "Válvula " + String(valveId) + " aberta";
    } else {
      result = "ID de válvula inválido";
    }

  } else if (commandType == "valve_close") {
    int valveId = parameters["valve_id"] | 1;
    if (valveId >= 1 && valveId <= numValves) {
      closeValve(valveId - 1);
      success = true;
      result = "Válvula " + String(valveId) + " fechada";
    } else {
      result = "ID de válvula inválido";
    }

  } else if (commandType == "read_sensors") {
    readSensors();
    success = true;
    result = "Temp: " + String(lastTemperature) + "°C, Pressão: " + String(lastPressure) + " bar";

  } else {
    result = "Comando não reconhecido: " + commandType;
  }

  // Enviar resultado do comando
  sendCommandResult(commandId, success, result);
}

void sendCommandResult(int commandId, bool success, String result) {
  if (!wifiConnected) return;

  HTTPClient http;
  http.begin(String(serverURL) + "?action=command_result");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Authorization", "Bearer " + String(apiKey));

  DynamicJsonDocument doc(1024);
  doc["command_id"] = commandId;
  doc["result"]["success"] = success;
  doc["result"]["message"] = result;
  doc["result"]["timestamp"] = timeClient.getEpochTime();

  String jsonString;
  serializeJson(doc, jsonString);

  int httpResponseCode = http.POST(jsonString);

  if (httpResponseCode > 0) {
    Serial.println("Resultado do comando enviado: " + result);
  } else {
    Serial.println("Erro ao enviar resultado: " + String(httpResponseCode));
  }

  http.end();
}

void openValve(int valveIndex) {
  if (valveIndex >= 0 && valveIndex < numValves) {
    digitalWrite(valvePins[valveIndex], HIGH);
    valveStates[valveIndex] = true;
    Serial.println("Válvula " + String(valveIndex + 1) + " aberta");

    // Log local
    logOperation("VALVE_OPEN", valveIndex + 1, true);
  }
}

void closeValve(int valveIndex) {
  if (valveIndex >= 0 && valveIndex < numValves) {
    digitalWrite(valvePins[valveIndex], LOW);
    valveStates[valveIndex] = false;
    Serial.println("Válvula " + String(valveIndex + 1) + " fechada");

    // Log local
    logOperation("VALVE_CLOSE", valveIndex + 1, false);
  }
}

void logOperation(String operation, int valveId, bool state) {
  // Log para LittleFS
  File logFile = LittleFS.open("/operations.log", "a");
  if (logFile) {
    String logEntry = String(timeClient.getEpochTime()) + "," + operation + "," + String(valveId) + "," + String(state) + "\n";
    logFile.print(logEntry);
    logFile.close();
  }
}

void updateStatusLED() {
  static unsigned long lastBlink = 0;
  static bool ledState = false;

  unsigned long currentTime = millis();

  if (wifiConnected && serverConnected) {
    // LED fixo quando tudo está conectado
    digitalWrite(statusLED, HIGH);
  } else if (wifiConnected) {
    // Piscar lento quando só WiFi está conectado
    if (currentTime - lastBlink >= 1000) {
      ledState = !ledState;
      digitalWrite(statusLED, ledState);
      lastBlink = currentTime;
    }
  } else {
    // Piscar rápido quando desconectado
    if (currentTime - lastBlink >= 200) {
      ledState = !ledState;
      digitalWrite(statusLED, ledState);
      lastBlink = currentTime;
    }
  }
}

void blinkStatusLED(int times, int delayMs) {
  for (int i = 0; i < times; i++) {
    digitalWrite(statusLED, HIGH);
    delay(delayMs);
    digitalWrite(statusLED, LOW);
    delay(delayMs);
  }
}
