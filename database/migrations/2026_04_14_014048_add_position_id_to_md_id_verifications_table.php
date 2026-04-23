<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('md_id_verifications', function (Blueprint $table) {
        // Tambahkan position_id setelah main_dealer_id
        $table->foreignId('position_id')
            ->nullable()
            ->after('main_dealer_id')
            ->constrained('positions')
            ->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('md_id_verifications', function (Blueprint $table) {
        $table->dropForeign(['position_id']);
        $table->dropColumn('position_id');
    });
}
};
