<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('point_histories', function (Blueprint $table) {
        $table->integer('xp_amount')->default(0)->after('amount');
    });
}

public function down(): void
{
    Schema::table('point_histories', function (Blueprint $table) {
        $table->dropColumn('xp_amount');
    });
}

  
};
