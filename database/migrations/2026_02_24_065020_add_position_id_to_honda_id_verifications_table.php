<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('honda_id_verifications', function (Blueprint $table) {
            $table->unsignedBigInteger('position_id')->nullable()->after('dealer_id');
            
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('honda_id_verifications', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};