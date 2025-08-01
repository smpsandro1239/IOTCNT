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
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('valve_id')->nullable()->constrained('valves')->onDelete('set null');
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User from web portal
            // telegram_user_id will be added after telegram_users table is created to avoid foreign key issues during migration order
            $table->string('event_type', 100);
            $table->text('message');
            $table->enum('source', ['SYSTEM', 'ESP32', 'WEB_PORTAL', 'TELEGRAM_BOT', 'SCHEDULED_TASK'])->default('SYSTEM');
            $table->enum('status', ['SUCCESS', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'])->default('INFO');
            $table->json('details')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            // No $table->timestamps(); as logged_at serves as created_at and logs are typically immutable.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_logs');
    }
};
