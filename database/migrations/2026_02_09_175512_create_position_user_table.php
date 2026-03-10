<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Opsi: Hapus kolom position_id lama di users jika sudah tidak dipakai
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropForeign(['position_id']);
        //     $table->dropColumn('position_id');
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_user');
        
        // Schema::table('users', function (Blueprint $table) {
        //     $table->foreignId('position_id')->nullable()->constrained();
        // });
    }
};