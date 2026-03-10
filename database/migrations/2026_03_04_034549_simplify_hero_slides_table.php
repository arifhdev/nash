<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel lama beserta isinya biar tidak ada sisa kolom yang bentrok
        Schema::dropIfExists('hero_slides');

        // Buat ulang tabel dengan struktur super simpel
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};