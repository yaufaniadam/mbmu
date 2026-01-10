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
            $table->foreignId('pj_id')->nullable()->after('kepala_sppg_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppg', function (Blueprint $table) {
            $table->dropForeign(['pj_id']);
            $table->dropColumn('pj_id');
        });
    }
};
