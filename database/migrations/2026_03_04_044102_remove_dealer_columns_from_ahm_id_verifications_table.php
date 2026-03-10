<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ahm_id_verifications', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu agar aman
            $table->dropForeign(['main_dealer_id']);
            $table->dropForeign(['dealer_id']);
            
            // Baru hapus kolomnya
            $table->dropColumn(['main_dealer_id', 'dealer_id']);
        });
    }

    public function down(): void
    {
        Schema::table('ahm_id_verifications', function (Blueprint $table) {
            // Jika suatu saat ingin dikembalikan (opsional)
            $table->foreignId('main_dealer_id')->nullable()->constrained('main_dealers')->nullOnDelete();
            $table->foreignId('dealer_id')->nullable()->constrained('dealers')->nullOnDelete();
        });
    }
};