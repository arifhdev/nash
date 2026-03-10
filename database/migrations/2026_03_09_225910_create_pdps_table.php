<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdps', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Persetujuan Pemrosesan Data Pribadi (PDP)');
            $table->longText('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdps');
    }
};