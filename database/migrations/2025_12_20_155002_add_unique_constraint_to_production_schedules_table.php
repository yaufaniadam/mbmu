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
        // First, clean up any existing duplicates
        // Keep only the most recent record for each sppg_id + tanggal combination
        DB::statement("
            DELETE t1 FROM jadwal_produksi t1
            INNER JOIN jadwal_produksi t2 
            WHERE 
                t1.sppg_id = t2.sppg_id 
                AND t1.tanggal = t2.tanggal
                AND t1.id < t2.id
        ");

        Schema::table('jadwal_produksi', function (Blueprint $table) {
            // Add unique constraint: one distribution plan per SPPG per day
            $table->unique(['sppg_id', 'tanggal'], 'unique_sppg_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_produksi', function (Blueprint $table) {
            $table->dropUnique('unique_sppg_date');
        });
    }
};
