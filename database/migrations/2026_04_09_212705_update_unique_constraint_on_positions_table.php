<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('positions', function (Blueprint $table) {
        // Gunakan nama asli dari hasil SHOW INDEX tadi
        $table->dropUnique('position_group_unique'); 

        // Buat index baru yang menyertakan user_type
        $table->unique(['name', 'divisi', 'user_type'], 'position_group_unique');
    });
}

public function down(): void
{
    Schema::table('positions', function (Blueprint $table) {
        $table->dropUnique('position_group_unique');
        $table->unique(['name', 'divisi'], 'position_group_unique');
    });
}
};
