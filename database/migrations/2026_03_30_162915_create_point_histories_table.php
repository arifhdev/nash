<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Berapa poin yang didapat (atau dikurangi jika ada sistem redeem nanti)
            $table->integer('amount'); 
            
            // Deskripsi aksi (ex: "Daily Login", "Selesai Course: Matic 101")
            $table->string('description'); 
            
            // Polymorphic relation untuk melacak sumber poin (opsional tapi sangat disarankan)
            // ex: source_type = 'App\Models\Course', source_id = 1
            $table->nullableMorphs('source'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_histories');
    }
};