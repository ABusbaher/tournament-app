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
        Schema::table('tournaments', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE tournaments MODIFY COLUMN type VARCHAR(255)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE tournaments MODIFY COLUMN type ENUM('league', 'elimination', 'group+elimination')");
            }
        });
    }
};
