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
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('class');
            $table->json('classes')->nullable()->after('race');
            $table->string('background')->nullable()->after('race');
            $table->string('alignment')->nullable()->after('background');
            $table->json('inventory')->nullable()->after('charisma');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->string('class')->after('race');
            $table->dropColumn('classes');
            $table->dropColumn('background');
            $table->dropColumn('alignment');
            $table->dropColumn('inventory');
        });
    }
};
