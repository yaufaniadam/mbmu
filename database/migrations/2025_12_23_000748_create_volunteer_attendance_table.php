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
        Schema::create('volunteer_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained('relawan')->cascadeOnDelete();
            $table->foreignId('sppg_id')->constrained('sppg')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('days_present')->default(0);
            $table->integer('late_minutes')->default(0);
            $table->decimal('daily_rate', 15, 2); // Copied from volunteer table when created
            $table->decimal('late_deduction_per_hour', 15, 2)->default(0);
            $table->decimal('gross_salary', 15, 2)->default(0); // Auto-calculated: days_present * daily_rate
            $table->decimal('late_deduction', 15, 2)->default(0); // Auto-calculated: (late_minutes / 60) * late_deduction_per_hour
            $table->decimal('net_salary', 15, 2)->default(0); // Auto-calculated: gross_salary - late_deduction
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure one attendance record per volunteer per period
            $table->unique(['volunteer_id', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_attendance');
    }
};
