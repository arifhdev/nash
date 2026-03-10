<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_id_verifications', function (Blueprint $table) {
            // Menambahkan kolom main_dealer_id setelah kolom custom_id
            $table->foreignId('main_dealer_id')->nullable()->after('custom_id')->constrained('main_dealers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('custom_id_verifications', function (Blueprint $table) {
            $table->dropForeign(['main_dealer_id']);
            $table->dropColumn('main_dealer_id');
        });
    }
};