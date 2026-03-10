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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            
            // Tipe Lesson
            $table->string('type')->default('video'); // video, text, quiz
            
            // Konten
            $table->string('video_url')->nullable();
            $table->longText('content')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->json('quiz_data')->nullable();
            
            $table->boolean('is_free')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
