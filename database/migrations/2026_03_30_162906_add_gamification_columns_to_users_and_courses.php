<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cache total poin agar query leaderboard cepat
            $table->integer('total_points')->default(0)->after('email'); 
        });

        Schema::table('courses', function (Blueprint $table) {
            // Setup poin yang didapat user saat menyelesaikan course
            $table->integer('points_reward')->default(0)->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_points');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('points_reward');
        });
    }
};
