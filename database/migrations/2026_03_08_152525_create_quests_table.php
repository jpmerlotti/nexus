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
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'first_character_created'
            $table->string('name');
            $table->text('description');
            $table->unsignedInteger('reward_nex')->default(0);
            $table->boolean('is_repeatable')->default(false);
            $table->timestamps();
        });

        Schema::create('user_quest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quest_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('earned_nex');
            $table->timestamp('completed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_quest');
        Schema::dropIfExists('quests');
    }
};
