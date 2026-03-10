<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_module', function (Blueprint $table) {
            $table->id();
            // Menghubungkan Course ke Modul
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            
            // Kolom urutan modul di dalam course
            $table->integer('sort_order')->default(0); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_module');
    }
};