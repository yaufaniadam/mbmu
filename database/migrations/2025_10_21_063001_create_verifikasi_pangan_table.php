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
        Schema::create('verifikasi_pangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_produksi_id')->constrained('jadwal_produksi')->cascadeOnDelete();
            $table->foreignId('user_id')->comment('User Staf Gizi')->constrained('users');
            $table->json('checklist_data');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_pangan');
    }
};
