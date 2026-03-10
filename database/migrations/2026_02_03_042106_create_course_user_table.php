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
    Schema::create('course_user', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('course_id')->constrained()->cascadeOnDelete();
        
        // Untuk track progress global kursus ini (0-100%)
        $table->integer('progress_percent')->default(0);
        
        // Status: active, completed
        $table->enum('status', ['active', 'completed'])->default('active');
        
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();

        // Mencegah user enroll kursus yang sama 2 kali
        $table->unique(['user_id', 'course_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_user');
    }
};
