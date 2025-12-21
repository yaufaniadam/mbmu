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
        Schema::table('production_verifications', function (Blueprint $table) {
            $table->foreignId('production_schedule_id')->nullable()->constrained('jadwal_produksi')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_verifications', function (Blueprint $table) {
            $table->dropForeign(['production_schedule_id']);
            $table->dropColumn('production_schedule_id');
        });
    }
};
