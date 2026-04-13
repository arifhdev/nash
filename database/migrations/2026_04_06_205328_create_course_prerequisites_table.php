<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke course utama
            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->cascadeOnDelete();
                  
            // Relasi ke course yang menjadi prasyarat
            $table->foreignId('prerequisite_id')
                  ->constrained('courses')
                  ->cascadeOnDelete();
                  
            $table->timestamps();

            // Mencegah insert duplikat untuk pasangan course yang sama
            $table->unique(['course_id', 'prerequisite_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
    }
};