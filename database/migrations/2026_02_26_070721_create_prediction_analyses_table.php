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
        Schema::create('prediction_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->string('metric_type'); // temperature, humidity, pressure, etc.
            $table->decimal('current_value', 10, 2);
            $table->decimal('moving_average', 10, 2)->nullable();
            $table->enum('trend_direction', ['increasing', 'decreasing', 'stable', 'insufficient_data'])->default('insufficient_data');
            $table->decimal('predicted_value', 10, 2)->nullable();
            $table->decimal('threshold_min', 10, 2)->nullable();
            $table->decimal('threshold_max', 10, 2)->nullable();
            $table->enum('alert_level', ['normal', 'warning', 'critical'])->default('normal');
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamp('prediction_timestamp')->default(now());
            $table->timestamps();
            
            // Índices para performance
            $table->index(['device_id', 'metric_type']);
            $table->index('prediction_timestamp');
            $table->index('alert_level');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediction_analyses');
    }
};
