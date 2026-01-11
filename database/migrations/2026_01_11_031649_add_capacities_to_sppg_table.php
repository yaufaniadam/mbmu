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
        Schema::table('sppg', function (Blueprint $table) {
            $table->integer('porsi_besar')->default(0)->after('balance');
            $table->integer('porsi_kecil')->default(0)->after('porsi_besar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppg', function (Blueprint $table) {
            $table->dropColumn(['porsi_besar', 'porsi_kecil']);
        });
    }
};
