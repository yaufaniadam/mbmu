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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('sppg_id')->constrained('sppg')->onDelete('cascade');
            $table->enum('type', ['SPPG_SEWA', 'LP_ROYALTY']);
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['UNPAID', 'WAITING_VERIFICATION', 'PAID', 'REJECTED'])->default('UNPAID');
            $table->string('proof_of_payment')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('due_date');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
