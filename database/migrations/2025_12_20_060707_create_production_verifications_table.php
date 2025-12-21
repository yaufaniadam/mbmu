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
        Schema::dropIfExists('production_verifications');
        Schema::create('production_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sppg_id');
            $table->unsignedBigInteger('user_id'); // Who verified it
            $table->date('date');
            
            // Stores the actual checklist result as JSON
            // e.g. [{"item": "Rasa", "status": "Layak"}, {"item": "Suhu", "status": "Aman"}]
            $table->json('checklist_results')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('sppg_id')->references('id')->on('sppg')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_verifications');
    }
};
