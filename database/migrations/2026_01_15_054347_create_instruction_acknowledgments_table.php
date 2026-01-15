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
        Schema::create('instruction_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instruction_id')->constrained('instructions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('acknowledged_at');
            $table->timestamps();
            
            // Ensure a user can only acknowledge an instruction once
            $table->unique(['instruction_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instruction_acknowledgments');
    }
};
