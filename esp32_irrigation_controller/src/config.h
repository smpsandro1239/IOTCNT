#ifndef CONFIG_H
#define CONFIG_H

// ==========================================================================
// ESP32 Irrigation Controller Configuration - EXAMPLE
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
#define WIFI_SSID "SUA_REDE_WIFI"
#define WIFI_PASSWORD "SUA_SENHA_WIFI"
#define WIFI_TIMEOUT_MS 20000  // 20 seconds timeout for WiFi connection

// --------------------------------------------------------------------------
// API Server Configuration
// --------------------------------------------------------------------------
#define API_SERVER_HOST "http://seu_laravel_server.com"  // Change to your server
#define API_TOKEN "SEU_TOKEN_API_SANCTUM_AQUI"           // Your Sanctum token
#define API_ENDPOINT_VALVE_STATUS "/api/esp32/valve-status"
#define API_ENDPOINT_LOG "/api/esp32/log"
#define API_ENDPOINT_CONFIG "/api/esp32/config"
#define API_ENDPOINT_COMMANDS "/api/esp32/commands"

// --------------------------------------------------------------------------
// Hardware Configuration
// --------------------------------------------------------------------------
// Valve relay pins (adjust according to your hardware)
#define VALVE_PIN_1 23
#define VALVE_PIN_2 22
#define VALVE_PIN_3 21
#define VALVE_PIN_4 19
#define VALVE_PIN_5 18

#define NUM_VALVES 5
#define RELAY_ON_STATE HIGH   // Change to LOW if your relays are active low
#define RELAY_OFF_STATE LOW   // Change to HIGH if your relays are active low

// --------------------------------------------------------------------------
// Scheduling Configuration
// --------------------------------------------------------------------------
#define SCHEDULE_DAY 5        // 0=Sunday, 1=Monday, ..., 5=Friday, 6=Saturday
#define SCHEDULE_HOUR 10      // 10 AM
#define SCHEDULE_MINUTE 0     // 00 minutes
#define VALVE_DURATION_MINUTES 5  // Default duration per valve

// --------------------------------------------------------------------------
// Time Configuration
// --------------------------------------------------------------------------
#define NTP_SERVER "pool.ntp.org"
#define GMT_OFFSET_SEC 0      // GMT offset in seconds (0 for UTC)
#define DAYLIGHT_OFFSET_SEC 3600  // Daylight saving offset (3600 for +1 hour)
#define TIME_SYNC_INTERVAL_MS 3600000  // Sync time every hour

// --------------------------------------------------------------------------
// Logging Configuration
// --------------------------------------------------------------------------
#define LOG_FILE "/irrigation_log.txt"
#define MAX_LOG_SIZE (10 * 1024)  // 10KB max log file size
#define ENABLE_SERIAL_DEBUG true
#define ENABLE_FILE_LOGGING true
#define ENABLE_API_LOGGING true

// --------------------------------------------------------------------------
// System Configuration
// --------------------------------------------------------------------------
#define MAIN_LOOP_DELAY_MS 1000     // Main loop delay
#define API_RETRY_ATTEMPTS 3        // Number of API retry attempts
#define API_TIMEOUT_MS 10000        // API request timeout
#define COMMAND_POLL_INTERVAL_MS 30000  // Poll for commands every 30 seconds

// --------------------------------------------------------------------------
// Debug Configuration
// --------------------------------------------------------------------------
#define DEBUG_WIFI true
#define DEBUG_API true
#define DEBUG_SCHEDULING true
#define DEBUG_VALVES true

// --------------------------------------------------------------------------
// Optional Features
// --------------------------------------------------------------------------
#define ENABLE_RTC false      // Set to true if using RTC module
#define ENABLE_LORA false     // Set to true if using LoRa communication
#define ENABLE_OTA false      // Set to true for Over-The-Air updates

// --------------------------------------------------------------------------
// LoRa Configuration (if enabled)
// --------------------------------------------------------------------------
#if ENABLE_LORA
#define LORA_SCK 5
#define LORA_MISO 19
#define LORA_MOSI 27
#define LORA_SS 18
#define LORA_RST 14
#define LORA_DIO0 26
#define LORA_BAND 868E6  // 868MHz for Europe, 915MHz for US, 433MHz for Asia
#endif

// --------------------------------------------------------------------------
// RTC Configuration (if enabled)
// --------------------------------------------------------------------------
#if ENABLE_RTC
#define RTC_SDA 21
#define RTC_SCL 22
#endif

#endif // CONFIG_H
