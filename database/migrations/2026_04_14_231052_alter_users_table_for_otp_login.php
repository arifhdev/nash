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
            // Ubah email dan password jadi BISA KOSONG (nullable)
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            
            // Jadikan nomor HP UNIQUE tapi tetap izinkan NULL untuk data lama
            $table->string('phone_number')->nullable()->unique()->change();
        });

        // Pisahkan penambahan kolom baru dengan pengecekan
        // Agar tidak error jika kolom sudah terlanjur dibuat di percobaan sebelumnya
        if (!Schema::hasColumn('users', 'division_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('division_id')->nullable()->after('dealer_id')->constrained('divisions')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan seperti semula jika di-rollback
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
            
            // Hapus unique constraint pada phone_number
            $table->dropUnique(['phone_number']);
        });

        if (Schema::hasColumn('users', 'division_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['division_id']);
                $table->dropColumn('division_id');
            });
        }
    }
};