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
    Schema::table('users', function (Blueprint $table) {
        // total_points yang lama kita anggap sebagai 'Currency' (bisa berkurang)
        // Kita tambah total_xp untuk Leaderboard (Life-time)
        $table->integer('total_xp')->default(0)->after('total_points');
    });

    Schema::table('courses', function (Blueprint $table) {
        // Admin bisa set: Selesai course ini dapat berapa XP dan berapa Point
        $table->integer('xp_reward')->default(0)->after('points_reward');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
