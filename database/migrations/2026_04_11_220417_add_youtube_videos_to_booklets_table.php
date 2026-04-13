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
    Schema::table('booklets', function (Blueprint $table) {
        $table->json('youtube_videos')->nullable()->after('pdf_file');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booklets', function (Blueprint $table) {
            $table->dropColumn('youtube_videos');
        });
    }
};
