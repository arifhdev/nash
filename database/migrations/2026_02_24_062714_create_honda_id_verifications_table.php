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
        Schema::create('honda_id_verifications', function (Blueprint $table) {
            $table->id();
            
            // Kolom honda_id (dibuat unique agar tidak ada duplikat ID)
            $table->string('honda_id')->unique();
            
            // Kolom is_active untuk mengecek status aktif/tidaknya ID tersebut
            $table->boolean('is_active')->default(true);
            
            // Kolom sudah punya akun? (kita namakan has_account)
            $table->boolean('has_account')->default(false);
            
            // Relasi ke tabel main_dealers dan dealers
            $table->unsignedBigInteger('main_dealer_id')->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable();
            
            $table->timestamps();

            // Mendefinisikan Foreign Key (Opsional tapi sangat direkomendasikan untuk integritas data)
            $table->foreign('main_dealer_id')->references('id')->on('main_dealers')->onDelete('set null');
            $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('honda_id_verifications');
    }
};