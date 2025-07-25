<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $settings = [
      [
        'key' => 'default_valve_duration',
        'value' => '5',
        'type' => 'integer',
        'description' => 'Default duration in minutes for each valve',
        'is_public' => true,
      ],
      [
        'key' => 'emergency_stop_enabled',
        'value' => 'true',
        'type' => 'boolean',
        'description' => 'Allow emergency stop commands',
        'is_public' => true,
      ],
      [
        'key' => 'telegram_notifications_enabled',
        'value' => 'true',
        'type' => 'boolean',
        'description' => 'Enable Telegram notifications',
        'is_public' => false,
      ],
      [
        'key' => 'max_cycle_duration_minutes',
        'value' => '60',
        'type' => 'integer',
        'description' => 'Maximum duration for a complete irrigation cycle',
        'is_public' => true,
      ],
      [
        'key' => 'system_timezone',
        'value' => 'Europe/Lisbon',
        'type' => 'string',
        'description' => 'System timezone',
        'is_public' => true,
      ],
      [
        'key' => 'maintenance_mode',
        'value' => 'false',
        'type' => 'boolean',
        'description' => 'Enable maintenance mode',
        'is_public' => false,
      ],
      [
        'key' => 'log_retention_days',
        'value' => '30',
        'type' => 'integer',
        'description' => 'Number of days to keep operation logs',
        'is_public' => false,
      ],
      [
        'key' => 'esp32_heartbeat_interval',
        'value' => '300',
        'type' => 'integer',
        'description' => 'ESP32 heartbeat interval in seconds',
        'is_public' => true,
      ],
      [
        'key' => 'auto_backup_enabled',
        'value' => 'true',
        'type' => 'boolean',
        'description' => 'Enable automatic database backups',
        'is_public' => false,
      ],
      [
        'key' => 'backup_retention_days',
        'value' => '7',
        'type' => 'integer',
        'description' => 'Number of days to keep database backups',
        'is_public' => false,
      ],
    ];

    foreach ($settings as $settingData) {
      SystemSetting::firstOrCreate(
        ['key' => $settingData['key']],
        $settingData
      );
    }
  }
}
