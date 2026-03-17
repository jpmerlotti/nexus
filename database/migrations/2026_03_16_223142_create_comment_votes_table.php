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
        Schema::create('comment_votes', function (Blueprint $table) {
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->string('ip_hash');
            $table->tinyInteger('vote_type'); // 1 for upvote, -1 for downvote
            $table->timestamps();

            $table->primary(['comment_id', 'ip_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_votes');
    }
};
