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
    Schema::create('lesson_module', function (Blueprint $table) {
        $table->id();
        $table->foreignId('module_id')->constrained()->cascadeOnDelete();
        $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
        $table->integer('sort_order')->default(0); // Agar bisa diurutkan per modul
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_module');
    }
};
