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
        Schema::table('relawan', function (Blueprint $table) {
            $table->string('category')->nullable()->after('posisi'); // Masak, Asisten, Pengantaran, Kebersihan, Umum
            $table->foreignId('user_id')->nullable()->after('sppg_id')->constrained('users')->nullOnDelete();
            $table->string('nik')->nullable()->after('nama_relawan');
            $table->decimal('daily_rate', 15, 2)->nullable()->after('kontak'); // Untuk payroll ke depan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('relawan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['category', 'user_id', 'nik', 'daily_rate']);
        });
    }
};
