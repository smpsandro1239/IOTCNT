# Project Structure

## Root Directory Organization

```
├── esp32_irrigation_controller/     # ESP32 firmware
│   └── esp32_irrigation_controller.ino
├── app/                            # Laravel application logic
│   ├── Http/Controllers/           # API and web controllers
│   ├── Models/                     # Eloquent models
│   └── View/Components/            # Blade components
├── resources/views/                # Blade templates
│   ├── admin/                      # Admin interface views
│   ├── components/                 # Reusable view components
│   ├── layouts/                    # Layout templates
│   └── dashboard.blade.php         # Main dashboard
├── routes/                         # Route definitions
│   ├── api.php                     # API endpoints
│   └── web.php                     # Web routes
├── *.php                          # Database migrations
└── README.md                       # Project documentation
```

## Laravel Application Structure

### Models (`app/Models/`)
- `Valve.php` - Irrigation valve entities
- `Schedule.php` - Timing and automation rules
- `OperationLog.php` - System activity tracking
- `TelegramUser.php` - Telegram bot user management

### Database Migrations (Root Level)
- `create_users_table.php` - User authentication
- `create_valves_table.php` - Valve configuration
- `create_schedules_table.php` - Irrigation schedules
- `create_telegram_users_table.php` - Telegram integration
- `create_operation_logs_table.php` - Activity logging
- `add_telegram_user_id_to_operation_logs_table.php` - Schema update

## Code Organization Patterns

### ESP32 Firmware
- Single `.ino` file with modular function organization
- Configuration constants at top of file
- Setup/loop pattern with helper functions
- API communication functions grouped together
- Hardware control functions separated from networking

### Laravel Backend
- Standard MVC architecture
- RESTful API design for ESP32 communication
- Eloquent relationships between models
- Migration-based database schema management
- Role-based access control (admin/user)

## Key Architectural Decisions
- ESP32 communicates via HTTP REST API (not MQTT)
- Laravel Sanctum for API authentication
- JSON format for all API communications
- Local logging on ESP32 with remote API logging
- Telegram bot as additional control interface
- Time synchronization via NTP with RTC fallback
