<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    // 1. Buat tabel master divisi
    Schema::create('divisions', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('code')->unique(); // Misal: LOG, H1, H2, PART
        $table->timestamps();
    });

    // 2. Tambahkan foreign key ke tabel positions
    Schema::table('positions', function (Blueprint $table) {
        $table->foreignId('division_id')
            ->nullable()
            ->after('id')
            ->constrained('divisions')
            ->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('positions', function (Blueprint $table) {
        $table->dropForeign(['division_id']);
        $table->dropColumn('division_id');
    });
    Schema::dropIfExists('divisions');
}
};
