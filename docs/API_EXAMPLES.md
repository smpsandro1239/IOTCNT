# 🔧 IOTCNT - Exemplos Práticos da API

## 📱 Integração Frontend (JavaScript/Vue.js)

### Classe de Serviço API
```javascript
class IotcntApiService {
    constructor(baseUrl, token) {
        this.baseUrl = baseUrl;
        this.token = token;
        this.headers = {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
    }

    async getValveStatus() {
        const response = await fetch(`${this.baseUrl}/api/valve/status`, {
            headers: this.headers
        });
        return await response.json();
    }

    async controlValve(valveId, action, duration = 5) {
        const response = await fetch(`${this.baseUrl}/api/valve/control`, {
            method: 'POST',
            headers: {
                ...this.headers,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                valve_id: valveId,
                action: action,
                duration: duration
            })
        });
        return await response.json();
    }

    async startIrrigationCycle(durationPerValve = 5) {
        const response = await fetch(`${this.baseUrl}/api/valve/start-cycle`, {
            method: 'POST',
            headers: {
                ...this.headers,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                duration_per_valve: durationPerValve
            })
        });
        return await response.json();
    }

    async getSystemStats() {
        const response = await fetch(`${this.baseUrl}/api/valve/stats`, {
            headers: this.headers
        });
        return await response.json();
    }

    async getLogs(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${this.baseUrl}/api/logs?${params}`, {
            headers: this.headers
        });
        return await response.json();
    }
}

// Uso da classe
const api = new IotcntApiService('http://localhost:8000', 'seu_token_aqui');

// Obter estado das válvulas
api.getValveStatus().then(data => {
    console.log('Estado das válvulas:', data.valves);
});

// Controlar válvula
api.controlValve(1, 'on', 10).then(data => {
    if (data.success) {
        console.log('Válvula controlada com sucesso');
    }
});
```

### Componente Vue.js para Dashboard
```vue
<template>
  <div class="irrigation-dashboard">
    <div class="stats-cards">
      <div class="stat-card">
        <h3>Válvulas Ativas</h3>
        <span class="stat-value">{{ stats.active_valves }}</span>
      </div>
      <div class="stat-card">
        <h3>Operações Hoje</h3>
        <span class="stat-value">{{ stats.total_operations_today }}</span>
      </div>
    </div>

    <div class="valves-grid">
      <div
        v-for="valve in valves"
        :key="valve.id"
        class="valve-card"
        :class="{ active: valve.current_state }"
      >
        <h4>{{ valve.name }}</h4>
        <div class="valve-status">
          <span :class="valve.current_state ? 'status-on' : 'status-off'">
            {{ valve.current_state ? 'LIGADA' : 'DESLIGADA' }}
          </span>
        </div>
        <div class="valve-controls">
          <button
            @click="controlValve(valve.id, 'toggle')"
            :disabled="loading"
            class="btn-toggle"
          >
            {{ valve.current_state ? 'Desligar' : 'Ligar' }}
          </button>
        </div>
      </div>
    </div>

    <div class="system-controls">
      <button
        @click="startCycle"
        :disabled="loading"
        class="btn-cycle"
      >
        Iniciar Ciclo Completo
      </button>
      <button
        @click="stopAll"
        :disabled="loading"
        class="btn-stop"
      >
        Parar Todas
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'IrrigationDashboard',
  data() {
    return {
      valves: [],
      stats: {},
      loading: false,
      api: null
    };
  },
  mounted() {
    this.api = new IotcntApiService(
      process.env.VUE_APP_API_URL,
      this.$store.state.auth.token
    );
    this.loadData();
    this.setupRealTimeUpdates();
  },
  methods: {
    async loadData() {
      try {
        const [valveData, statsData] = await Promise.all([
          this.api.getValveStatus(),
          this.api.getSystemStats()
        ]);

        this.valves = valveData.valves;
        this.stats = statsData.stats;
      } catch (error) {
        console.error('Erro ao carregar dados:', error);
        this.$toast.error('Erro ao carregar dados do sistema');
      }
    },

    async controlValve(valveId, action) {
      this.loading = true;
      try {
        const result = await this.api.controlValve(valveId, action);
        if (result.success) {
          this.$toast.success(result.message);
          await this.loadData(); // Recarregar dados
        }
      } catch (error) {
        this.$toast.error('Erro ao controlar válvula');
      } finally {
        this.loading = false;
      }
    },

    async startCycle() {
      this.loading = true;
      try {
        const result = await this.api.startIrrigationCycle(5);
        if (result.success) {
          this.$toast.success('Ciclo de irrigação iniciado');
        }
      } catch (error) {
        this.$toast.error('Erro ao iniciar ciclo');
      } finally {
        this.loading = false;
      }
    },

    setupRealTimeUpdates() {
      // WebSocket para atualizações em tempo real
      Echo.channel('irrigation-system')
        .listen('ValveStatusChanged', (e) => {
          const valve = this.valves.find(v => v.id === e.valve.id);
          if (valve) {
            valve.current_state = e.valve.current_state;
            valve.last_activated_at = e.valve.last_activated_at;
          }
        });
    }
  }
};
</script>
```

## 🤖 Integração ESP32 (C++)

### Classe Principal ESP32
```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <NTPClient.h>
#include <WiFiUdp.h>

class IotcntEsp32Client {
private:
    String apiUrl;
    String apiToken;
    HTTPClient http;

public:
    IotcntEsp32Client(String url, String token) : apiUrl(url), apiToken(token) {}

    bool getConfiguration() {
        http.begin(apiUrl + "/api/esp32/config");
        http.addHeader("Authorization", "Bearer " + apiToken);
        http.addHeader("Accept", "application/json");

        int httpCode = http.GET();

        if (httpCode == HTTP_CODE_OK) {
            String payload = http.getString();
            DynamicJsonDocument doc(4096);
            deserializeJson(doc, payload);

            if (doc["success"]) {
                processConfiguration(doc["data"]);
                return true;
            }
        }

        http.end();
        return false;
    }

    bool reportValveStatus(int valveNumber, bool state) {
        http.begin(apiUrl + "/api/esp32/valve-status");
        http.addHeader("Authorization", "Bearer " + apiToken);
        http.addHeader("Content-Type", "application/json");

        DynamicJsonDocument doc(256);
        doc["valve_number"] = valveNumber;
        doc["state"] = state;
        doc["timestamp_device"] = WiFi.getTime();

        String jsonString;
        serializeJson(doc, jsonString);

        int httpCode = http.POST(jsonString);
        http.end();

        return httpCode == HTTP_CODE_OK;
    }

    bool sendLog(int valveNumber, String action, int duration, String notes = "") {
        http.begin(apiUrl + "/api/esp32/log");
        http.addHeader("Authorization", "Bearer " + apiToken);
        http.addHeader("Content-Type", "application/json");

        DynamicJsonDocument doc(512);
        doc["valve_number"] = valveNumber;
        doc["action"] = action;
        doc["duration_minutes"] = duration;
        doc["timestamp_device"] = WiFi.getTime();
        doc["notes"] = notes;

        String jsonString;
        serializeJson(doc, jsonString);

        int httpCode = http.POST(jsonString);
        http.end();

        return httpCode == HTTP_CODE_OK;
    }

    std::vector<Command> getCommands() {
        std::vector<Command> commands;

        http.begin(apiUrl + "/api/esp32/commands");
        http.addHeader("Authorization", "Bearer " + apiToken);

        int httpCode = http.GET();

        if (httpCode == HTTP_CODE_OK) {
            String payload = http.getString();
            DynamicJsonDocument doc(2048);
            deserializeJson(doc, payload);

            if (doc["success"]) {
                JsonArray commandsArray = doc["commands"];
                for (JsonObject cmd : commandsArray) {
                    Command command;
                    command.id = cmd["id"];
                    command.type = cmd["type"].as<String>();
                    command.valveNumber = cmd["valve_number"];
                    command.action = cmd["action"].as<String>();
                    command.duration = cmd["duration"];
                    commands.push_back(command);
                }
            }
        }

        http.end();
        return commands;
    }

private:
    void processConfiguration(JsonObject config) {
        // Processar configuração das válvulas
        JsonArray valves = config["valves"];
        for (JsonObject valve : valves) {
            int valveNumber = valve["valve_number"];
            int pin = valve["esp32_pin"];
            bool state = valve["current_state"];

            // Configurar pino e estado inicial
            pinMode(pin, OUTPUT);
            digitalWrite(pin, state ? HIGH : LOW);
        }

        // Processar agendamentos
        JsonArray schedules = config["schedules"];
        for (JsonObject schedule : schedules) {
            // Armazenar agendamentos para processamento
            // Implementar lógica de agendamento
        }
    }
};

// Estrutura para comandos
struct Command {
    int id;
    String type;
    int valveNumber;
    String action;
    int duration;
};
```

### Loop Principal ESP32
```cpp
#include "config.h"

IotcntEsp32Client* apiClient;
unsigned long lastConfigUpdate = 0;
unsigned long lastCommandCheck = 0;
unsigned long lastStatusReport = 0;

const unsigned long CONFIG_UPDATE_INTERVAL = 300000; // 5 minutos
const unsigned long COMMAND_CHECK_INTERVAL = 10000;  // 10 segundos
const unsigned long STATUS_REPORT_INTERVAL = 60000;  // 1 minuto

void setup() {
    Serial.begin(115200);

    // Conectar WiFi
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.println("Conectando ao WiFi...");
    }

    Serial.println("WiFi conectado!");
    Serial.print("IP: ");
    Serial.println(WiFi.localIP());

    // Inicializar cliente API
    apiClient = new IotcntEsp32Client(API_SERVER_URL, API_TOKEN);

    // Obter configuração inicial
    if (apiClient->getConfiguration()) {
        Serial.println("Configuração obtida com sucesso");
    } else {
        Serial.println("Erro ao obter configuração");
    }

    // Configurar pinos das válvulas
    for (int i = 0; i < 5; i++) {
        pinMode(VALVE_PINS[i], OUTPUT);
        digitalWrite(VALVE_PINS[i], LOW);
    }
}

void loop() {
    unsigned long currentTime = millis();

    // Verificar comandos pendentes
    if (currentTime - lastCommandCheck >= COMMAND_CHECK_INTERVAL) {
        checkForCommands();
        lastCommandCheck = currentTime;
    }

    // Reportar estado das válvulas
    if (currentTime - lastStatusReport >= STATUS_REPORT_INTERVAL) {
        reportAllValveStatus();
        lastStatusReport = currentTime;
    }

    // Atualizar configuração
    if (currentTime - lastConfigUpdate >= CONFIG_UPDATE_INTERVAL) {
        apiClient->getConfiguration();
        lastConfigUpdate = currentTime;
    }

    // Processar agendamentos
    processSchedules();

    delay(1000);
}

void checkForCommands() {
    std::vector<Command> commands = apiClient->getCommands();

    for (const Command& cmd : commands) {
        if (cmd.type == "valve_control") {
            executeValveCommand(cmd);
        }
    }
}

void executeValveCommand(const Command& cmd) {
    int valveIndex = cmd.valveNumber - 1;

    if (valveIndex >= 0 && valveIndex < 5) {
        bool newState = (cmd.action == "on");

        digitalWrite(VALVE_PINS[valveIndex], newState ? HIGH : LOW);

        // Reportar mudança de estado
        apiClient->reportValveStatus(cmd.valveNumber, newState);

        // Enviar log
        apiClient->sendLog(
            cmd.valveNumber,
            cmd.action,
            cmd.duration,
            "Command executed from API"
        );

        Serial.printf("Válvula %d %s\n", cmd.valveNumber, newState ? "LIGADA" : "DESLIGADA");

        // Se tem duração, programar desligamento
        if (newState && cmd.duration > 0) {
            // Implementar timer para desligar após duração especificada
        }
    }
}

void reportAllValveStatus() {
    for (int i = 0; i < 5; i++) {
        bool state = digitalRead(VALVE_PINS[i]) == HIGH;
        apiClient->reportValveStatus(i + 1, state);
    }
}
```

## 🐍 Integração Python

### Cliente Python para Monitorização
```python
import requests
import json
import time
from datetime import datetime
import logging

class IotcntPythonClient:
    def __init__(self, base_url, token):
        self.base_url = base_url.rstrip('/')
        self.token = token
        self.session = requests.Session()
        self.session.headers.update({
            'Authorization': f'Bearer {token}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        })

    def get_valve_status(self):
        """Obtém estado de todas as válvulas"""
        try:
            response = self.session.get(f'{self.base_url}/api/valve/status')
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Erro ao obter estado das válvulas: {e}")
            return None

    def control_valve(self, valve_id, action, duration=5):
        """Controla uma válvula específica"""
        data = {
            'valve_id': valve_id,
            'action': action,
            'duration': duration
        }

        try:
            response = self.session.post(f'{self.base_url}/api/valve/control', json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Erro ao controlar válvula {valve_id}: {e}")
            return None

    def get_system_stats(self):
        """Obtém estatísticas do sistema"""
        try:
            response = self.session.get(f'{self.base_url}/api/valve/stats')
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Erro ao obter estatísticas: {e}")
            return None

    def get_logs(self, filters=None):
        """Obtém logs do sistema"""
        params = filters or {}

        try:
            response = self.session.get(f'{self.base_url}/api/logs', params=params)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Erro ao obter logs: {e}")
            return None

    def start_irrigation_cycle(self, duration_per_valve=5):
        """Inicia ciclo completo de irrigação"""
        data = {'duration_per_valve': duration_per_valve}

        try:
            response = self.session.post(f'{self.base_url}/api/valve/start-cycle', json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Erro ao iniciar ciclo: {e}")
            return None

# Exemplo de uso para monitorização
def monitor_system():
    client = IotcntPythonClient('http://localhost:8000', 'seu_token_aqui')

    while True:
        # Obter estado atual
        status = client.get_valve_status()
        if status and status['success']:
            print(f"\n=== Estado do Sistema - {datetime.now()} ===")

            for valve in status['valves']:
                state = "LIGADA" if valve['current_state'] else "DESLIGADA"
                print(f"Válvula {valve['valve_number']}: {state}")

                if valve['last_activated_at']:
                    print(f"  Última ativação: {valve['last_activated_at']}")

        # Obter estatísticas
        stats = client.get_system_stats()
        if stats and stats['success']:
            print(f"\nEstatísticas:")
            print(f"  Válvulas ativas: {stats['stats']['active_valves']}")
            print(f"  Operações hoje: {stats['stats']['total_operations_today']}")
            print(f"  Última atividade: {stats['stats']['last_activity']}")

        time.sleep(30)  # Verificar a cada 30 segundos

if __name__ == "__main__":
    logging.basicConfig(level=logging.INFO)
    monitor_system()
```

## 📊 Integração com Grafana/InfluxDB

### Script para Exportar Métricas
```python
from influxdb import InfluxDBClient
import json

class IotcntMetricsExporter:
    def __init__(self, iotcnt_client, influx_client):
        self.iotcnt = iotcnt_client
        self.influx = influx_client

    def export_valve_metrics(self):
        """Exporta métricas das válvulas para InfluxDB"""
        status = self.iotcnt.get_valve_status()

        if not status or not status['success']:
            return

        points = []
        timestamp = datetime.utcnow()

        for valve in status['valves']:
            point = {
                "measurement": "valve_status",
                "tags": {
                    "valve_id": valve['id'],
                    "valve_name": valve['name'],
                    "valve_number": valve['valve_number']
                },
                "fields": {
                    "current_state": 1 if valve['current_state'] else 0,
                    "esp32_pin": valve['esp32_pin']
                },
                "time": timestamp
            }
            points.append(point)

        self.influx.write_points(points)

    def export_system_stats(self):
        """Exporta estatísticas do sistema"""
        stats = self.iotcnt.get_system_stats()

        if not stats or not stats['success']:
            return

        point = {
            "measurement": "system_stats",
            "fields": {
                "total_valves": stats['stats']['total_valves'],
                "active_valves": stats['stats']['active_valves'],
                "inactive_valves": stats['stats']['inactive_valves'],
                "total_operations_today": stats['stats']['total_operations_today']
            },
            "time": datetime.utcnow()
        }

        self.influx.write_points([point])

# Configuração e uso
influx_client = InfluxDBClient('localhost', 8086, 'user', 'pass', 'iotcnt')
iotcnt_client = IotcntPythonClient('http://localhost:8000', 'token')
exporter = IotcntMetricsExporter(iotcnt_client, influx_client)

# Executar exportação a cada minuto
import schedule
schedule.every().minute.do(exporter.export_valve_metrics)
schedule.every().minute.do(exporter.export_system_stats)

while True:
    schedule.run_pending()
    time.sleep(1)
```

## 🔧 Testes Automatizados

### Testes da API com pytest
```python
import pytest
import requests

class TestIotcntAPI:
    def setup_method(self):
        self.base_url = "http://localhost:8000"
        self.token = "test_token"
        self.headers = {
            'Authorization': f'Bearer {self.token}',
            'Content-Type': 'application/json'
        }

    def test_get_valve_status(self):
        response = requests.get(
            f'{self.base_url}/api/valve/status',
            headers=self.headers
        )

        assert response.status_code == 200
        data = response.json()
        assert data['success'] is True
        assert 'valves' in data
        assert len(data['valves']) == 5

    def test_control_valve(self):
        payload = {
            'valve_id': 1,
            'action': 'on',
            'duration': 5
        }

        response = requests.post(
            f'{self.base_url}/api/valve/control',
            json=payload,
            headers=self.headers
        )

        assert response.status_code == 200
        data = response.json()
        assert data['success'] is True
        assert data['command_sent'] is True

    def test_start_irrigation_cycle(self):
        payload = {'duration_per_valve': 3}

        response = requests.post(
            f'{self.base_url}/api/valve/start-cycle',
            json=payload,
            headers=self.headers
        )

        assert response.status_code == 200
        data = response.json()
        assert data['success'] is True
        assert data['duration_per_valve'] == 3

    def test_get_system_stats(self):
        response = requests.get(
            f'{self.base_url}/api/valve/stats',
            headers=self.headers
        )

        assert response.status_code == 200
        data = response.json()
        assert data['success'] is True
        assert 'stats' in data
        assert 'total_valves' in data['stats']
```

Este conjunto de exemplos fornece uma base sólida para integração com a API IOTCNT em diferentes linguagens e contextos de uso.
