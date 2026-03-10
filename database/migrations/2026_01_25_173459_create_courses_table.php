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
    Schema::create('courses', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // Judul Kursus
        $table->string('slug')->unique(); // URL friendly (misal: basic-data-analytic)
        $table->string('image')->nullable(); // Foto kursus
        $table->text('description')->nullable(); // Deskripsi singkat
        $table->string('category'); // Kategori (Motivational, Selling Skill, dll)
        $table->date('start_date')->nullable(); // Tanggal: "19 Januari 2026"
        $table->integer('curriculum_count')->default(0); // Jumlah kurikulum: "9 Curriculum"
        $table->boolean('is_active')->default(true); // Status tampil/sembunyi
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
