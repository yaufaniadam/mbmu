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
        Schema::table('sppg_financial_reports', function (Blueprint $table) {
            $table->decimal('realization_amount', 15, 2)->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppg_financial_reports', function (Blueprint $table) {
            $table->dropColumn('realization_amount');
        });
    }
};
