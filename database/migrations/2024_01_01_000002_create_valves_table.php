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
        Schema::create('valves', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('valve_number')->unique(); // Assumindo até 255 válvulas, para 5 é mais que suficiente
            $table->text('description')->nullable();
            $table->boolean('current_state')->default(false);
            $table->timestamp('last_activated_at')->nullable();
            $table->unsignedTinyInteger('esp32_pin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valves');
    }
};
