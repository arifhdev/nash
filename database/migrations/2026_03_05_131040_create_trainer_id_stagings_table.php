<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_id_stagings', function (Blueprint $table) {
            $table->id();
            // Cukup simpan Trainer ID-nya saja untuk proses sinkronisasi sementara
            $table->string('trainer_id')->unique(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_id_stagings');
    }
};