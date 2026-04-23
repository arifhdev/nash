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
        // 1. Tambah kolom di tabel utama
        Schema::table('trainer_id_verifications', function (Blueprint $table) {
            $table->string('divisi')->nullable()->after('name');
            $table->string('jabatan')->nullable()->after('divisi');
        });

        // 2. Tambah kolom di tabel staging & Hapus Unique Index biar tidak crash saat import
        Schema::table('trainer_id_stagings', function (Blueprint $table) {
            $table->string('divisi')->nullable()->after('name');
            $table->string('jabatan')->nullable()->after('divisi');
            
            // Menghapus constraint unique pada trainer_id di staging
            $table->dropUnique(['trainer_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainer_id_verifications', function (Blueprint $table) {
            $table->dropColumn(['divisi', 'jabatan']);
        });

        Schema::table('trainer_id_stagings', function (Blueprint $table) {
            $table->dropColumn(['divisi', 'jabatan']);
            $table->unique('trainer_id'); // Kembalikan unique jika di-rollback
        });
    }
};