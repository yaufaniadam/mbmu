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
        Schema::create('jadwal_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppg_id')->constrained('sppg')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('menu_hari_ini');
            $table->enum('status', ['Direncanakan', 'Terverifikasi', 'Didistribusikan', 'Selesai'])->default('Direncanakan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_produksi');
    }
};
