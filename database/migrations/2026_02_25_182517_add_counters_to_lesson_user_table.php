<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('lesson_user', function (Blueprint $table) {
        $table->integer('count_view')->default(0)->after('course_id');
        $table->integer('count_completed')->default(0)->after('count_view');
    });
}

public function down()
{
    Schema::table('lesson_user', function (Blueprint $table) {
        $table->dropColumn(['count_view', 'count_completed']);
    });
}
};
