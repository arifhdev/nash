<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('klhn_events', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->date('event_date')->nullable();
        $table->text('description')->nullable();
        $table->json('gallery')->nullable(); // Kolom JSON untuk menyimpan banyak foto & caption
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klhn_events');
    }
};
