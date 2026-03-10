<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ahm_id_verifications', function (Blueprint $table) {
            // Menambahkan kolom name setelah ahm_id
            $table->string('name')->nullable()->after('ahm_id');
        });
    }

    public function down(): void
    {
        Schema::table('ahm_id_verifications', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};