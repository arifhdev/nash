<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('lesson_user', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
        // Course ID kita simpan juga biar gampang hitung progress per course
        $table->foreignId('course_id')->constrained()->cascadeOnDelete(); 
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
        
        // Mencegah user menyelesaikan lesson yang sama 2x (double data)
        $table->unique(['user_id', 'lesson_id', 'course_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_user');
    }
};
