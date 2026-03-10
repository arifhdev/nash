<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('custom_id_verifications', function (Blueprint $table) {
        // Tambahkan name setelah custom_id
        $table->string('name')->nullable()->after('custom_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_id_verifications', function (Blueprint $table) {
            //
        });
    }
};
