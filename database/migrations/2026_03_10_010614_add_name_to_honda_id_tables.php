<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('honda_id_verifications', function (Blueprint $table) {
            // Tambah kolom name setelah honda_id
            $table->string('name')->nullable()->after('honda_id');
        });

        Schema::table('honda_id_stagings', function (Blueprint $table) {
            // Tambah kolom name setelah honda_id
            $table->string('name')->nullable()->after('honda_id');
        });
    }

    public function down(): void
    {
        Schema::table('honda_id_verifications', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('honda_id_stagings', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};