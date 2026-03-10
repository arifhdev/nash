<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainer_id_verifications', function (Blueprint $table) {
            // Drop foreign key dulu, baru drop kolomnya
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
            
            $table->dropForeign(['dealer_id']);
            $table->dropColumn('dealer_id');
        });
    }

    public function down(): void
    {
        Schema::table('trainer_id_verifications', function (Blueprint $table) {
            // Rollback jika dibutuhkan
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('dealer_id')->nullable()->constrained('dealers')->nullOnDelete();
        });
    }
};