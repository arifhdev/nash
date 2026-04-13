<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            // Ubah nama kolom group menjadi divisi
            $table->renameColumn('group', 'divisi');
            
            // Tambah kolom user_type untuk membedakan jabatan AHM / Main Dealer / Dealer
            // Default 'dealer' agar data yang sudah ada (1 s/d 5) otomatis masuk ke Dealer
            $table->string('user_type')->default('dealer')->after('name'); 
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->renameColumn('divisi', 'group');
            $table->dropColumn('user_type');
        });
    }
};