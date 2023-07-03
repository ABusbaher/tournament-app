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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_team_id')->constrained('teams');
            $table->foreignId('guest_team_id')->nullable()->constrained('teams');
            $table->foreignId('tournament_id')->constrained('tournaments');
            $table->integer('host_goals')->nullable();
            $table->integer('guest_goals')->nullable();
            $table->integer('fixture');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
