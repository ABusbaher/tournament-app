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
        Schema::create('elimination_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team1_id')->nullable()->constrained('teams')->onDelete('cascade');;
            $table->foreignId('team2_id')->nullable()->constrained('teams')->onDelete('cascade');;
            $table->string('team1_prev')->nullable();
            $table->string('team2_prev')->nullable();
            $table->string('next_match')->nullable();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');;
            $table->integer('team1_goals')->nullable();
            $table->integer('team2_goals')->nullable();
            $table->integer('round');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elimination_games');
    }
};
