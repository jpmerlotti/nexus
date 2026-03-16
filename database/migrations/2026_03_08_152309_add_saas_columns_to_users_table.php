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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('nex_balance')->default(100)->after('password'); // 100 free nex for new users
            $table->text('custom_api_key')->nullable()->after('nex_balance'); // Encrypted Gemini API Key
            $table->string('ai_driver_preference')->default('platform')->after('custom_api_key'); // 'platform' or 'byok'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nex_balance', 'custom_api_key', 'ai_driver_preference']);
        });
    }
};
