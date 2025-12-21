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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppg_id')->constrained('sppg')->cascadeOnDelete();

            // Type: Who gets the money?
            $table->enum('type', ['sewa_lokal', 'setoran_kornas']);

            // Billed To: Who pays? (Polymorphic: SPPG or Pengusul)
            $table->morphs('billed_to');

            $table->string('invoice_number')->unique();

            // The Ledger Data (This allows us to remove the billing_periods table)
            $table->date('period_start');
            $table->date('period_end');

            $table->decimal('amount', 15, 2);
            $table->enum('status', ['unpaid', 'verification', 'paid', 'rejected'])->default('unpaid');
            $table->timestamps();

            // Index for performance (so the Robot can find the last date fast)
            $table->index(['sppg_id', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
