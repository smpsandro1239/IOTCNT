# 🔧 Guia de Configuração ESP32 - IOTCNT

## 📋 Visão Geral
Este guia explica como configurar e conectar dispositivos ESP32 ao sistema IOTCNT para controlo real de condensadores.

## 🛠️ Requisitos de Hardware

### ESP32 DevKit
- **Modelo**: ESP32-WROOM-32 ou compatível
- **Memória**: Mínimo 4MB Flash
- **Pinos**: Mínimo 30 pinos GPIO
- **Alimentação**: 5V via USB ou fonte externa

### Componentes Adicionais
- **Relés**: 5x Módulos de relé 5V (para controlo das válvulas)
- **Sensores**:
  - Sensor de temperatura DS18B20 ou similar
  - Sensor de pressão analógico 0-5V
- **Resistores**: Pull-up 4.7kΩ para sensores digitais
- **Cabos**: Jumpers e cabos de conexão
- **Fonte**: Fonte 5V/2A para alimentar relés

## 🔌 Esquema de Ligações

### Pinos das Válvulas (Relés)
```
GPIO 2  -> Relé 1 (Condensador 1)
GPIO 4  -> Relé 2 (Condensador 2)
GPIO 5  -> Relé 3 (Condensador 3)
GPIO 18 -> Relé 4 (Condensador 4)
GPIO 19 -> Relé 5 (Condensador 5)
```

### Pinos dos Sensores
```
GPIO 34 -> Sensor de Temperatura (Analógico)
GPIO 35 -> Sensor de Pressão (Analógico)
GPIO 2  -> LED de Estado (partilhado com Relé 1)
```

### Alimentação
```
VIN (5V) -> Fonte externa 5V
GND      -> Terra comum
3.3V     -> Sensores digitais (se aplicável)
```

## 💻 Configuração do Software

### 1. Instalar PlatformIO
```bash
# Via VS Code
# Instalar extensão PlatformIO IDE

# Via CLI
pip install platformio
```

### 2. Configurar Projeto
```bash
# Criar novo projeto
pio project init --board esp32dev

# Copiar código IOTCNT
cp esp32_iotcnt_v2.ino src/main.cpp
```

### 3. Configurar platformio.ini
```ini
[env:esp32dev]
platform = espressif32
board = esp32dev
framework = arduino
monitor_speed = 115200

lib_deps =
    bblanchon/ArduinoJson@^7.0.4
    arduino-libraries/NTPClient@^3.2.1
    adafruit/RTClib@^2.1.1
    lorol/LittleFS_esp32@^1.0.6
```

### 4. Configurar WiFi e Servidor
Editar no código `esp32_iotcnt_v2.ino`:

```cpp
// Configurações WiFi
const char* ssid = "SUA_REDE_WIFI";
const char* password = "SUA_SENHA_WIFI";

// Configurações do servidor IOTCNT
const char* serverURL = "http://SEU_IP:8080/esp32-integration.php";
const char* apiKey = "iotcnt-esp32-key-2025";

// Nome do dispositivo (único para cada ESP32)
String deviceName = "ESP32-IOTCNT-001";
```

## 🚀 Processo de Upload

### 1. Compilar e Upload
```bash
# Compilar
pio run

# Upload para ESP32
pio run --target upload

# Monitor serial
pio device monitor
```

### 2. Verificar Conexão
No monitor serial, deve aparecer:
```
=== ESP32 IOTCNT Controller v2.0 ===
MAC Address: XX:XX:XX:XX:XX:XX
WiFi conectado!
IP: 192.168.1.XXX
Dispositivo registado: {"status":"success"...}
Sistema inicializado com sucesso!
```

## 🌐 Integração com Sistema Web

### 1. Verificar no Dashboard
- Aceder a: `http://localhost:8080/esp32-dashboard.html`
- O dispositivo deve aparecer na lista como "Online"
- Verificar dados dos sensores em tempo real

### 2. Testar Comandos
- Seleccionar o dispositivo
- Enviar comando "Verificar Estado"
- Verificar resposta no monitor serial

### 3. Controlo de Válvulas
- Comando "Abrir Válvula" com parâmetro `{"valve_id": 1}`
- Comando "Fechar Válvula" com parâmetro `{"valve_id": 1}`
- Verificar activação física dos relés

## 🔧 Resolução de Problemas

### WiFi não Conecta
```cpp
// Verificar credenciais
const char* ssid = "NOME_CORRECTO";
const char* password = "SENHA_CORRECTA";

// Verificar sinal WiFi
Serial.println("RSSI: " + String(WiFi.RSSI()));
```

### Servidor não Responde
```cpp
// Verificar URL do servidor
const char* serverURL = "http://IP_CORRECTO:8080/esp32-integration.php";

// Testar conectividade
ping IP_DO_SERVIDOR
```

### Relés não Funcionam
- Verificar alimentação 5V dos relés
- Verificar ligações dos pinos GPIO
- Testar continuidade dos cabos
- Verificar se os relés são de 5V (não 3.3V)

### Sensores sem Dados
- Verificar ligações analógicas
- Testar com multímetro (0-3.3V)
- Verificar se os pinos ADC estão correctos

## 📊 Monitorização

### Logs Locais (LittleFS)
O ESP32 grava logs localmente em `/operations.log`:
```
timestamp,operation,valve_id,state
1692123456,VALVE_OPEN,1,true
1692123460,VALVE_CLOSE,1,false
```

### Logs no Servidor
Todos os comandos e estados são enviados para:
- Base de dados MySQL (tabelas esp32_*)
- Interface web (dashboard ESP32)
- Sistema de logs centralizado

### Heartbeat
- Enviado a cada 60 segundos
- Inclui estado das válvulas
- Dados dos sensores
- Informações do sistema (heap, RSSI, uptime)

## 🔒 Segurança

### Autenticação
- API Key obrigatória: `iotcnt-esp32-key-2025`
- MAC Address como identificador único
- Validação de comandos no servidor

### Rede
- Usar rede WiFi segura (WPA2/WPA3)
- Configurar firewall para porta 8080
- Considerar VPN para acesso remoto

## 📈 Expansão

### Múltiplos Dispositivos
Para adicionar mais ESP32:
1. Alterar `deviceName` para nome único
2. Upload do código para novo dispositivo
3. Dispositivo aparecerá automaticamente no dashboard

### Sensores Adicionais
Para adicionar mais sensores:
1. Definir novos pinos GPIO
2. Adicionar leitura no `readSensors()`
3. Incluir dados no heartbeat JSON

### Comandos Personalizados
Para novos comandos:
1. Adicionar case no `processCommand()`
2. Implementar lógica específica
3. Enviar resultado via `sendCommandResult()`

## 📞 Suporte

Para problemas técnicos:
1. Verificar logs no monitor serial
2. Consultar dashboard ESP32 no sistema web
3. Verificar conectividade de rede
4. Testar componentes individualmente

---

**Sistema IOTCNT - Integração ESP32 v2.0**
*Documentação técnica para implementação de hardware real*
