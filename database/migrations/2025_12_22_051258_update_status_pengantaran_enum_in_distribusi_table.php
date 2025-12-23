<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE distribusi MODIFY COLUMN status_pengantaran ENUM('Menunggu', 'Sedang Dikirim', 'Terkirim', 'Selesai') DEFAULT 'Menunggu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Reverting might fail if there are 'Selesai' values in the column.
        // We should handle that or just accept truncation if specifically requested, 
        // but typically we don't revert enums without cleaning data.
        DB::statement("ALTER TABLE distribusi MODIFY COLUMN status_pengantaran ENUM('Menunggu', 'Sedang Dikirim', 'Terkirim') DEFAULT 'Menunggu'");
    }
};
