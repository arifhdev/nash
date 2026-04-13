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
        Schema::table('ahm_id_verifications', function (Blueprint $table) {
            // Menambahkan foreign key position_id yang berelasi ke tabel positions
            $table->foreignId('position_id')
                  ->nullable()
                  ->after('name') 
                  ->constrained('positions')
                  ->nullOnDelete(); // Jika jabatan dihapus, kolom ini jadi null (tidak ikut terhapus)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ahm_id_verifications', function (Blueprint $table) {
            // Hapus constraint foreign key dulu, baru hapus kolomnya
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};