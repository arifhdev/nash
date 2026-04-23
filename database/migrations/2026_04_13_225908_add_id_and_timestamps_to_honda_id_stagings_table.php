<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('honda_id_stagings', function (Blueprint $table) {
            // Tambahkan ID di urutan paling depan
            $table->id()->first(); 
            // Tambahkan created_at dan updated_at
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('honda_id_stagings', function (Blueprint $table) {
            $table->dropColumn(['id', 'created_at', 'updated_at']);
        });
    }
};