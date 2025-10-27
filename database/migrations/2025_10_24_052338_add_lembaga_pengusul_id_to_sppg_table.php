<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sppg', function (Blueprint $table) {
            $table->foreignId('lembaga_pengusul_id')
                  ->nullable()
                  ->after('kepala_sppg_id') // Atau sesuaikan posisinya
                  ->constrained('lembaga_pengusul')
                  ->onDelete('set null'); // Jika lembaga dihapus, SPPG tidak ikut terhapus
        });
    }

    public function down(): void
    {
        Schema::table('sppg', function (Blueprint $table) {
            // Hapus constraint dulu, baru drop kolom
            $table->dropForeign(['lembaga_pengusul_id']);
            $table->dropColumn('lembaga_pengusul_id');
        });
    }
};