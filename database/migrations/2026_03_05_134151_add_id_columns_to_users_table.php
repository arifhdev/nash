<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan ketiga kolom ID tersebut tepat setelah honda_id
            $table->string('ahm_id')->nullable()->unique()->after('honda_id');
            $table->string('trainer_id')->nullable()->unique()->after('ahm_id');
            $table->string('custom_id')->nullable()->unique()->after('trainer_id'); // Ini untuk MD ID
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus jika di-rollback
            $table->dropColumn(['ahm_id', 'trainer_id', 'custom_id']);
        });
    }
};