<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honda_id_stagings', function (Blueprint $table) {
            $table->string('honda_id')->index(); // Index wajib biar proses JOIN MySQL kilat
            $table->string('md_code')->nullable();
            $table->string('dealer_code')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('group')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honda_id_stagings');
    }
};