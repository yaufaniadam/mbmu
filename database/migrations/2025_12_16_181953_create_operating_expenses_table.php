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
        Schema::create('operating_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('previous_version_id')->nullable()->constrained('operating_expenses')->nullOnDelete();
            $table->foreignId('sppg_id')->nullable()->constrained('sppg')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->date('date')->nullable();
            $table->string('category')->nullable();
            $table->text('attachment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operating_expenses');
    }
};
