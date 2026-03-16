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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('narration_detail_level')->default('normal'); // ex: succinct, normal, detailed
            $table->string('difficulty')->default('normal'); // ex: easy, normal, hard
            $table->integer('starting_level')->default(1);
            $table->string('play_style')->default('balanced'); // ex: combat_focused, roleplay, balanced
            $table->string('progression_type')->default('xp'); // ex: xp, milestone
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
