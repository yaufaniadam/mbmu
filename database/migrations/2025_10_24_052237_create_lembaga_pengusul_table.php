<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembaga_pengusul', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lembaga');
            $table->text('alamat_lembaga');


            // Tambahkan satu foreign key ke tabel users
            $table->foreignId('pimpinan_id')
                  ->nullable()
                  ->constrained('users') // Merujuk ke tabel 'users'
                  ->onDelete('set null'); // Jika user pimpinan dihapus, lembaga tetap ada
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembaga_pengusul');
    }
};