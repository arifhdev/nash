<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('user_activities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('activity_type'); // Contoh: 'Login', 'Logout', 'Membuka Course'
        $table->string('ip_address')->nullable();
        $table->text('user_agent')->nullable();
        $table->timestamps(); // otomatis membuat created_at dan updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
