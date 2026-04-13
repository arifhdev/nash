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
        Schema::table('courses', function (Blueprint $table) {
            // Menambahkan kolom require_sequential setelah has_certificate
            $table->boolean('require_sequential')
                  ->default(true)
                  ->after('has_certificate')
                  ->comment('True jika semua materi di dalam course ini harus dikerjakan berurutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn('require_sequential');
        });
    }
};