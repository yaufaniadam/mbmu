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
        Schema::create('volunteer_daily_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained('relawan')->cascadeOnDelete();
            $table->foreignId('sppg_id')->constrained('sppg')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->default('Alpha');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // One attendance record per volunteer per day
            $table->unique(['volunteer_id', 'attendance_date']);
            
            // Index for faster queries
            $table->index(['sppg_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_daily_attendance');
    }
};
