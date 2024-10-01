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
        Schema::table('games', function (Blueprint $table) {
            $table->dateTime('game_time')->nullable()->after('guest_goals');
        });
        Schema::table('elimination_games', function (Blueprint $table) {
            $table->dateTime('game_time')->nullable()->after('team2_goals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('game_time');
        });
        Schema::table('elimination_games', function (Blueprint $table) {
            $table->dropColumn('game_time');
        });
    }
};
