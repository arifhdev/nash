<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('ahm_id_verifications', function (Blueprint $table) {
        $table->dropForeign(['position_id']);
        $table->dropColumn('position_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ahm_id_verifications', function (Blueprint $table) {
            //
        });
    }
};
