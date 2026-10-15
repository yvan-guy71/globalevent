<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('competition_candidates')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('value')->comment('1 = vote pour, -1 = vote contre');
            $table->timestamps();

            $table->unique(['candidate_id', 'user_id']);
            $table->index(['event_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_votes');
    }
};
