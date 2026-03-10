<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('course_user', function (Blueprint $table) {
        $table->timestamp('last_accessed_at')->nullable()->after('completed_at');
    });
}

public function down()
{
    Schema::table('course_user', function (Blueprint $table) {
        $table->dropColumn('last_accessed_at');
    });
}
};
