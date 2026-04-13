<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ahm_id_stagings', function (Blueprint $table) {
            // Tambahkan kolom string untuk menampung data mentah dari Excel
            $table->string('divisi')->nullable()->after('name');
            $table->string('jabatan')->nullable()->after('divisi');
        });
    }

    public function down(): void
    {
        Schema::table('ahm_id_stagings', function (Blueprint $table) {
            $table->dropColumn(['divisi', 'jabatan']);
        });
    }
};