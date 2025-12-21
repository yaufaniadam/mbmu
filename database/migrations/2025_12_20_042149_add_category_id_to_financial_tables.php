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
        Schema::table('operating_expenses', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('operating_expense_categories')->nullOnDelete();
        });

        Schema::table('sppg_incoming_funds', function (Blueprint $table) {
             $table->foreignId('category_id')->nullable()->constrained('sppg_incoming_fund_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operating_expenses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::table('sppg_incoming_funds', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
