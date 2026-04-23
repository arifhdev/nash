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
        Schema::table('honda_id_stagings', function (Blueprint $table) {
            // Mengganti nama kolom 'group' menjadi 'divisi'
            $table->renameColumn('group', 'divisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('honda_id_stagings', function (Blueprint $table) {
            // Mengembalikan 'divisi' menjadi 'group' jika di-rollback
            $table->renameColumn('divisi', 'group');
        });
    }
};