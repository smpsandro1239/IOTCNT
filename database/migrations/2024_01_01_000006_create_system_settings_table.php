<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('system_settings', function (Blueprint $table) {
      $table->id();
      $table->string('key')->unique();
      $table->text('value')->nullable();
      $table->string('type')->default('string'); // string, integer, boolean, json
      $table->text('description')->nullable();
      $table->boolean('is_public')->default(false); // Can be accessed by ESP32
      $table->timestamps();
    });

    // Insert default settings
    DB::table('system_settings')->insert([
      [
        'key' => 'default_valve_duration',
        'value' => '5',
        'type' => 'integer',
        'description' => 'Default duration in minutes for each valve',
        'is_public' => true,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'key' => 'emergency_stop_enabled',
        'value' => 'true',
        'type' => 'boolean',
        'description' => 'Allow emergency stop commands',
        'is_public' => true,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'key' => 'telegram_notifications_enabled',
        'value' => 'true',
        'type' => 'boolean',
        'description' => 'Enable Telegram notifications',
        'is_public' => false,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'key' => 'max_cycle_duration_minutes',
        'value' => '60',
        'type' => 'integer',
        'description' => 'Maximum duration for a complete irrigation cycle',
        'is_public' => true,
        'created_at' => now(),
        'updated_at' => now()
      ]
    ]);
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('system_settings');
  }
};
