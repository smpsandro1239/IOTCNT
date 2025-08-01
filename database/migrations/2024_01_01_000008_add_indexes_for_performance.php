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
    Schema::table('operation_logs', function (Blueprint $table) {
      // Add indexes for common queries
      $table->index(['source', 'logged_at'], 'idx_operation_logs_source_logged_at');
      $table->index(['status', 'logged_at'], 'idx_operation_logs_status_logged_at');
      $table->index(['event_type'], 'idx_operation_logs_event_type');
      $table->index(['logged_at'], 'idx_operation_logs_logged_at');
    });

    Schema::table('valves', function (Blueprint $table) {
      // Add index for valve number (frequently queried)
      $table->index(['valve_number'], 'idx_valves_valve_number');
      $table->index(['current_state'], 'idx_valves_current_state');
    });

    Schema::table('schedules', function (Blueprint $table) {
      // Add indexes for schedule queries
      $table->index(['is_enabled', 'day_of_week'], 'idx_schedules_enabled_day');
      $table->index(['day_of_week', 'start_time'], 'idx_schedules_day_time');
    });

    Schema::table('telegram_users', function (Blueprint $table) {
      // Add indexes for Telegram user queries
      $table->index(['is_authorized'], 'idx_telegram_users_authorized');
      $table->index(['authorization_level'], 'idx_telegram_users_auth_level');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('operation_logs', function (Blueprint $table) {
      $table->dropIndex('idx_operation_logs_source_logged_at');
      $table->dropIndex('idx_operation_logs_status_logged_at');
      $table->dropIndex('idx_operation_logs_event_type');
      $table->dropIndex('idx_operation_logs_logged_at');
    });

    Schema::table('valves', function (Blueprint $table) {
      $table->dropIndex('idx_valves_valve_number');
      $table->dropIndex('idx_valves_current_state');
    });

    Schema::table('schedules', function (Blueprint $table) {
      $table->dropIndex('idx_schedules_enabled_day');
      $table->dropIndex('idx_schedules_day_time');
    });

    Schema::table('telegram_users', function (Blueprint $table) {
      $table->dropIndex('idx_telegram_users_authorized');
      $table->dropIndex('idx_telegram_users_auth_level');
    });
  }
};
