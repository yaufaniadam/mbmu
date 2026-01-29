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
        Schema::table('menus', function (Blueprint $table) {
            $table->date('date')->nullable()->after('description');
        });
        
        // Populate existing records with created_at date to avoid nulls
        DB::table('menus')->update(['date' => DB::raw('DATE(created_at)')]);
        
        // Make it non-nullable after population (optional, but good for data integrity if we want it mandatory)
        // Schema::table('menus', function (Blueprint $table) {
        //     $table->date('date')->nullable(false)->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};
