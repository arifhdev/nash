<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ahm_id_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('ahm_id')->unique();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('main_dealer_id')->nullable()->constrained('main_dealers')->nullOnDelete();
            $table->foreignId('dealer_id')->nullable()->constrained('dealers')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('has_account')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ahm_id_verifications');
    }
};