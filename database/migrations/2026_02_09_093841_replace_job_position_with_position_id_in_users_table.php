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
            // 1. Tambahkan kolom baru (position_id)
            // Ditaruh setelah phone_number biar rapi
            $table->foreignId('position_id')
                  ->nullable()
                  ->after('phone_number')
                  ->constrained('positions')
                  ->nullOnDelete();

            // 2. Hapus kolom lama (job_position)
            $table->dropColumn('job_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan kolom lama
            $table->string('job_position')->nullable()->after('phone_number');

            // Hapus kolom baru
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};