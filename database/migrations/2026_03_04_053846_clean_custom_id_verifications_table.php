<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('custom_id_verifications', function (Blueprint $table) {
        // Hapus foreign keys agar tidak error saat drop column
        $table->dropForeign(['position_id']);
        $table->dropForeign(['main_dealer_id']);
        $table->dropForeign(['dealer_id']);
        
        // Hapus kolomnya
        $table->dropColumn(['position_id', 'main_dealer_id', 'dealer_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
