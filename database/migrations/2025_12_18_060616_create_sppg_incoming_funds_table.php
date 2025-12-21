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
        Schema::create('sppg_incoming_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('previous_version_id')->nullable()->constrained('sppg_incoming_funds')->nullOnDelete();
            $table->foreignId('sppg_id')->nullable()->constrained('sppg')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->decimal('amount', 15, 2)->default(0);
            $table->string('source')->nullable();
            $table->date('received_at')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('sppg_incoming_funds');
    }
};
