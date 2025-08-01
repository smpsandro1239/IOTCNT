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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Rega Semanal Principal');
            $table->tinyInteger('day_of_week')->unsigned(); // 0 (Domingo) - 6 (Sábado)
            $table->time('start_time');
            $table->unsignedSmallInteger('per_valve_duration_minutes')->default(5);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
