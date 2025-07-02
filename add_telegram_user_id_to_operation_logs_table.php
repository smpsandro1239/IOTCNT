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
            // Add the column after 'user_id' or any other appropriate column
            $table->foreignId('telegram_user_id')->nullable()->after('user_id')->constrained('telegram_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_logs', function (Blueprint $table) {
            // It's good practice to drop the foreign key constraint before dropping the column
            $table->dropForeign(['telegram_user_id']);
            $table->dropColumn('telegram_user_id');
        });
    }
};
