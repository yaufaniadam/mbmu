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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppg_id')->constrained('sppg')->cascadeOnDelete();
            $table->string('nama_sekolah');
            $table->text('alamat');
           
             // Gunakan char/string sesuai tipe kolom 'code' di laravolt
            $table->char('province_code', 2)->nullable();
            $table->char('city_code', 4)->nullable();
            $table->char('district_code', 7)->nullable();
            $table->char('village_code', 10)->nullable();

            $table->timestamps();

            $table->foreign('province_code')->references('code')->on('indonesia_provinces')->onDelete('set null');
            $table->foreign('city_code')->references('code')->on('indonesia_cities')->onDelete('set null');
            $table->foreign('district_code')->references('code')->on('indonesia_districts')->onDelete('set null');
            $table->foreign('village_code')->references('code')->on('indonesia_villages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
