<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('main_dealers', function (Blueprint $table) {
            // 1. Tambahkan kolom address (karena ternyata belum ada di database)
            // Kita taruh setelah 'code'
            $table->text('address')->nullable()->after('code');

            // 2. Tambahkan kolom group
            // Kita taruh setelah 'address' yang baru dibuat
            $table->string('group')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('main_dealers', function (Blueprint $table) {
            $table->dropColumn(['group', 'address']);
        });
    }
};
