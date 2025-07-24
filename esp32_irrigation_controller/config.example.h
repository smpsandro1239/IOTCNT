#ifndef CONFIG_H
#define CONFIG_H

// ==========================================================================
// ESP32 Irrigation Controller Configuration Example
// ==========================================================================
//
// IMPORTANTE:
// 1. Copie este arquivo para config.h
// 2. Configure todas as variáveis com seus valores reais
// 3. NUNCA commite o arquivo config.h no Git
//

// --------------------------------------------------------------------------
// WiFi Configuration
// --------------------------------------------------------------------------
#define WIFI_SSID "SUA_REDE_WIFI"                    // ⚠️ ALTERAR
#define WIFI_PASSWORD "SUA_SENHA_WIFI"               // ⚠️ ALTERAR
#define WIFI_TIMEOUT_MS 20000                        // 20 seconds timeout for WiFi connection

// Configurações avançadas WiFi (opcional)
#define WIFI_MAX_RETRIES 5                           // Máximo de tentativas de conexão
#define WIFI_RETRY_DELAY_MS 5000                     // Delay entre tentativas (5 segundos)

// --------------------------------------------------------------------------
// API Server Configuration
// --------------------------------------------------------------------------
#define API_SERVER_HOST "http://192.168.1.100"       // ⚠️ ALTERAR - IP do seu servidor
// Exemplos:
// Para desenvolvimento local: "http://192.168.1.100"
// Para produção: "https://seu-dominio.com"
// Para Docker local: "http://host.docker.internal"

#define API_TOKEN "SEU_TOKEN_API_SANCTUM_AQUI"       // ⚠️ ALTERAR - Token do Laravel Sanctum

// Endpoints da API (normalmente não precisam ser alterados)
#define API_ENDPOINT_VALVE_STATUS "/api/esp32/valve-status"
#define API_ENDPOINT_LOG "/api/esp32/log"
#define API_ENDPOINT_CONFIG "/api/esp32/config"
#define API_ENDPOINT_COMMANDS "/api/esp32/commands"

// --------------------------------------------------------------------------
// Hardware Configuration
// --------------------------------------------------------------------------
// Pinos das válvulas (ajuste conforme seu hardware)
#define VALVE_PIN_1 23                               // ⚠️ VERIFICAR/ALTERAR
#define VALVE_PIN_2 22                               // ⚠️ VERIFICAR/ALTERAR
#define VALVE_PIN_3 21                               // ⚠️ VERIFICAR/ALTERAR
#define VALVE_PIN_4 19                               // ⚠️ VERIFICAR/ALTERAR
#define VALVE_PIN_5 18                               // ⚠️ VERIFICAR/ALTERAR

#define NUM_VALVES 5                                 // Número de válvulas (máximo 5)

// Configuração dos relés
#define RELAY_ON_STATE HIGH                          // ⚠️ VERIFICAR - HIGH ou LOW conforme seu módulo relé
#define RELAY_OFF_STATE LOW                          // ⚠️ VERIFICAR - LOW ou HIGH conforme seu módulo relé

// Pinos adicionais (opcional)
#define LED_STATUS_PIN 2                             // LED de status (GPIO2 - LED interno)
#define BUTTON_PIN 0                                 // Botão para reset/configuração (GPIO0)

// --------------------------------------------------------------------------
// Scheduling Configuration
// --------------------------------------------------------------------------
#define SCHEDULE_DAY 5                               // 0=Domingo, 1=Segunda, ..., 5=Sexta, 6=Sábado
#define SCHEDULE_HOUR 10                             // Hora (0-23)
#define SCHEDULE_MINUTE 0                            // Minuto (0-59)
#define VALVE_DURATION_MINUTES 5                     // Duração padrão por válvula (minutos)

// --------------------------------------------------------------------------
// Time Configuration
// --------------------------------------------------------------------------
#define NTP_SERVER "pool.ntp.org"                    // Servidor NTP
// Servidores NTP alternativos:
// "time.google.com"
// "time.cloudflare.com"
// "pt.pool.ntp.org" (Portugal)
// "br.pool.ntp.org" (Brasil)

#define GMT_OFFSET_SEC 0                             // ⚠️ ALTERAR - Offset GMT em segundos
// Exemplos:
// Portugal (inverno): 0
// Portugal (verão): 3600
// Brasil (Brasília): -10800
// Brasil (Acre): -18000

#define DAYLIGHT_OFFSET_SEC 3600                     // Offset horário de verão (3600 = +1 hora)
#define TIME_SYNC_INTERVAL_MS 3600000                // Sincronizar hora a cada hora

// --------------------------------------------------------------------------
// Logging Configuration
// --------------------------------------------------------------------------
#define LOG_FILE "/irrigation_log.txt"               // Arquivo de log no sistema de arquivos
#define MAX_LOG_SIZE (10 * 1024)                     // Tamanho máximo do log (10KB)
#define ENABLE_SERIAL_DEBUG true                     // Ativar debug via Serial
#define ENABLE_FILE_LOGGING true                     // Ativar logging em arquivo
#define ENABLE_API_LOGGING true                      // Ativar envio de logs para API

// Níveis de log
#define LOG_LEVEL_DEBUG 0
#define LOG_LEVEL_INFO 1
#define LOG_LEVEL_WARNING 2
#define LOG_LEVEL_ERROR 3
#define CURRENT_LOG_LEVEL LOG_LEVEL_INFO             // Nível mínimo de log

// --------------------------------------------------------------------------
// System Configuration
// --------------------------------------------------------------------------
#define MAIN_LOOP_DELAY_MS 1000                      // Delay do loop principal
#define API_RETRY_ATTEMPTS 3                         // Tentativas de retry para API
#define API_TIMEOUT_MS 10000                         // Timeout para requisições API (10 segundos)
#define COMMAND_POLL_INTERVAL_MS 30000               // Polling de comandos a cada 30 segundos
#define HEARTBEAT_INTERVAL_MS 300000                 // Heartbeat a cada 5 minutos

// Configurações de memória
#define JSON_BUFFER_SIZE 2048                        // Tamanho do buffer JSON
#define HTTP_BUFFER_SIZE 4096                        // Tamanho do buffer HTTP

// --------------------------------------------------------------------------
// Debug Configuration
// --------------------------------------------------------------------------
#define DEBUG_WIFI true                              // Debug de WiFi
#define DEBUG_API true                               // Debug de API
#define DEBUG_SCHEDULING true                        // Debug de agendamento
#define DEBUG_VALVES true                            // Debug de válvulas
#define DEBUG_TIME true                              // Debug de sincronização de tempo

// --------------------------------------------------------------------------
// Optional Features
// --------------------------------------------------------------------------
#define ENABLE_RTC false                             // ⚠️ ALTERAR - true se usar módulo RTC
#define ENABLE_LORA false                            // ⚠️ ALTERAR - true se usar LoRa
#define ENABLE_OTA false                             // ⚠️ ALTERAR - true para updates OTA
#define ENABLE_WEB_SERVER false                      // ⚠️ ALTERAR - true para servidor web local
#define ENABLE_MQTT false                            // ⚠️ ALTERAR - true para MQTT

// --------------------------------------------------------------------------
// RTC Configuration (se ENABLE_RTC = true)
// --------------------------------------------------------------------------
#if ENABLE_RTC
#define RTC_SDA 21                                   // Pino SDA do RTC
#define RTC_SCL 22                                   // Pino SCL do RTC
#define RTC_INTERRUPT_PIN 4                          // Pino de interrupção do RTC (opcional)
#endif

// --------------------------------------------------------------------------
// LoRa Configuration (se ENABLE_LORA = true)
// --------------------------------------------------------------------------
#if ENABLE_LORA
#define LORA_SCK 5                                   // Pino SCK do LoRa
#define LORA_MISO 19                                 // Pino MISO do LoRa
#define LORA_MOSI 27                                 // Pino MOSI do LoRa
#define LORA_SS 18                                   // Pino SS do LoRa
#define LORA_RST 14                                  // Pino RST do LoRa
#define LORA_DIO0 26                                 // Pino DIO0 do LoRa
#define LORA_BAND 868E6                              // Frequência LoRa
// Frequências por região:
// Europa: 868E6
// América do Norte: 915E6
// Ásia: 433E6
#endif

// --------------------------------------------------------------------------
// OTA Configuration (se ENABLE_OTA = true)
// --------------------------------------------------------------------------
#if ENABLE_OTA
#define OTA_PASSWORD "senha_ota_segura"              // ⚠️ ALTERAR - Senha para OTA
#define OTA_PORT 3232                                // Porta para OTA
#define OTA_HOSTNAME "ESP32-IOTCNT"                  // Nome do dispositivo para OTA
#endif

// --------------------------------------------------------------------------
// Web Server Configuration (se ENABLE_WEB_SERVER = true)
// --------------------------------------------------------------------------
#if ENABLE_WEB_SERVER
#define WEB_SERVER_PORT 80                           // Porta do servidor web local
#define WEB_USERNAME "admin"                         // ⚠️ ALTERAR - Usuário para acesso web
#define WEB_PASSWORD "senha_web_segura"              // ⚠️ ALTERAR - Senha para acesso web
#endif

// --------------------------------------------------------------------------
// MQTT Configuration (se ENABLE_MQTT = true)
// --------------------------------------------------------------------------
#if ENABLE_MQTT
#define MQTT_SERVER "192.168.1.100"                 // ⚠️ ALTERAR - IP do servidor MQTT
#define MQTT_PORT 1883                               // Porta do MQTT
#define MQTT_USERNAME "mqtt_user"                    // ⚠️ ALTERAR - Usuário MQTT
#define MQTT_PASSWORD "mqtt_password"                // ⚠️ ALTERAR - Senha MQTT
#define MQTT_CLIENT_ID "ESP32-IOTCNT"                // ID do cliente MQTT
#define MQTT_TOPIC_PREFIX "iotcnt"                   // Prefixo dos tópicos MQTT
#endif

// --------------------------------------------------------------------------
// Safety Configuration
// --------------------------------------------------------------------------
#define MAX_VALVE_ON_TIME_MS (30 * 60 * 1000)       // Máximo 30 minutos por válvula (segurança)
#define EMERGENCY_STOP_ENABLED true                  // Ativar parada de emergência
#define WATCHDOG_TIMEOUT_MS 60000                    // Timeout do watchdog (60 segundos)

// --------------------------------------------------------------------------
// Device Information
// --------------------------------------------------------------------------
#define DEVICE_NAME "ESP32-IOTCNT"                   // Nome do dispositivo
#define FIRMWARE_VERSION "1.0.0"                     // Versão do firmware
#define HARDWARE_VERSION "1.0"                       // Versão do hardware

// --------------------------------------------------------------------------
// Validation and Warnings
// --------------------------------------------------------------------------
#if NUM_VALVES > 5
#error "Máximo de 5 válvulas suportadas"
#endif

#if VALVE_DURATION_MINUTES > 60
#warning "Duração por válvula muito alta (>60 min)"
#endif

#if API_TIMEOUT_MS < 5000
#warning "Timeout da API muito baixo (<5s)"
#endif

// --------------------------------------------------------------------------
// Notas Importantes:
// --------------------------------------------------------------------------
// 1. Teste todas as configurações antes de usar em produção
// 2. Verifique a pinagem do seu módulo ESP32
// 3. Confirme o tipo de relé (ativo alto ou baixo)
// 4. Use senhas fortes para OTA e Web Server
// 5. Configure o timezone corretamente
// 6. Teste a conectividade WiFi e API
// 7. Verifique os logs para diagnosticar problemas
// --------------------------------------------------------------------------

#endif // CONFIG_H
