# Technology Stack

## Backend Framework
- **Laravel** (PHP framework)
- **MySQL/MariaDB** database
- **Sanctum** for API authentication
- **Eloquent ORM** for database interactions

## Hardware Platform
- **ESP32** microcontroller
- **Arduino IDE/PlatformIO** for firmware development
- **C++** for embedded programming

## Key Libraries & Dependencies

### ESP32 Firmware
- `WiFi.h` - Network connectivity
- `HTTPClient.h` - API communication
- `ArduinoJson` (v7+) - JSON parsing/serialization
- `NTPClient` - Network time synchronization
- `RTClib` - Real-time clock support
- `LittleFS` - File system for local logging

### Laravel Backend
- Standard Laravel dependencies
- Database migrations for schema management
- Eloquent models for data relationships

## Database Schema
- `users` - User authentication and roles
- `valves` - Valve configuration and state
- `schedules` - Irrigation timing rules
- `telegram_users` - Telegram bot integration
- `operation_logs` - System activity tracking

## Common Commands

### Laravel Development
```bash
# Install dependencies
composer install

# Run migrations
php artisan migrate

# Start development server
php artisan serve

# Clear caches
php artisan cache:clear
php artisan config:clear
```

### ESP32 Development
```bash
# Using PlatformIO
pio run                 # Build firmware
pio upload              # Upload to device
pio device monitor      # Serial monitor
```

## Configuration Notes
- ESP32 requires WiFi credentials and API server configuration
- Laravel requires database connection and API token setup
- Telegram bot requires bot token and webhook configuration
