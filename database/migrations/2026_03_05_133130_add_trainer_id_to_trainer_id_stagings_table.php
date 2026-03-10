<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainer_id_stagings', function (Blueprint $table) {
            // Tambahkan kolom trainer_id setelah kolom id
            $table->string('trainer_id')->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('trainer_id_stagings', function (Blueprint $table) {
            $table->dropColumn('trainer_id');
        });
    }
};