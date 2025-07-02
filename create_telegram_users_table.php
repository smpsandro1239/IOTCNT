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
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_chat_id')->unique()->index();
            $table->string('telegram_username')->nullable()->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null');
            $table->boolean('is_authorized')->default(false);
            $table->enum('authorization_level', ['admin', 'user'])->nullable();
            $table->boolean('receive_notifications')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
