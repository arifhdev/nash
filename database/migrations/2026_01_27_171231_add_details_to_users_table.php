<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Data User Baru
        $table->string('honda_id')->nullable()->unique()->after('id');
        $table->string('phone_number')->nullable()->after('email'); // No WA
        $table->string('job_position')->nullable()->after('phone_number'); // Jabatan
        
        // Flagging User (Menggunakan Enum nanti di model)
        $table->string('user_type')->default('non_dealer')->after('job_position');

        // Relasi ke tabel yang baru kita buat
        $table->foreignId('main_dealer_id')->nullable()->constrained('main_dealers')->nullOnDelete()->after('user_type');
        $table->foreignId('dealer_id')->nullable()->constrained('dealers')->nullOnDelete()->after('main_dealer_id');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['main_dealer_id']);
        $table->dropForeign(['dealer_id']);
        $table->dropColumn(['honda_id', 'phone_number', 'job_position', 'user_type', 'main_dealer_id', 'dealer_id']);
    });
}
};
