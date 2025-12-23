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
        // Use raw SQL to modify the ENUM column
        // Laravel's Blueprint doesn't support modifying ENUMs directly
        \DB::statement("ALTER TABLE `jadwal_produksi` MODIFY COLUMN `status` ENUM('Direncanakan', 'Menunggu ACC Kepala SPPG', 'Terverifikasi', 'Didistribusikan', 'Ditolak', 'Selesai') NOT NULL DEFAULT 'Direncanakan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original ENUM values
        \DB::statement("ALTER TABLE `jadwal_produksi` MODIFY COLUMN `status` ENUM('Direncanakan', 'Terverifikasi', 'Didistribusikan', 'Selesai') NOT NULL DEFAULT 'Direncanakan'");
    }
};
