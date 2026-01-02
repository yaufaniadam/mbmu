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
            $table->dropColumn('period');
            $table->date('start_date')->after('user_id');
            $table->date('end_date')->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppg_financial_reports', function (Blueprint $table) {
            $table->date('period')->after('user_id');
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
