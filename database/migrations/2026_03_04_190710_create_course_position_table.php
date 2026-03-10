<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('course_position', function (Blueprint $table) {
        $table->id();
        // Hubungkan ke tabel courses
        $table->foreignId('course_id')->constrained()->onDelete('cascade');
        // Hubungkan ke tabel positions
        $table->foreignId('position_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_position');
    }
};
