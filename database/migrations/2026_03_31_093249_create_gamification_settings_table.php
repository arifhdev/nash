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
    Schema::create('gamification_settings', function (Blueprint $table) {
        $table->id();
        $table->integer('daily_checkin_points')->default(0); // Default poin mata uang
        $table->integer('daily_checkin_xp')->default(10);     // Default XP leaderboard
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_settings');
    }
};
