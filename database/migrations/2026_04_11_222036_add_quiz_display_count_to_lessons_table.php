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
        Schema::table('lessons', function (Blueprint $table) {
            // Tambahkan kolom quiz_display_count setelah quiz_data
            $table->integer('quiz_display_count')->nullable()->after('quiz_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Hapus kolom jika di-rollback
            $table->dropColumn('quiz_display_count');
        });
    }
};