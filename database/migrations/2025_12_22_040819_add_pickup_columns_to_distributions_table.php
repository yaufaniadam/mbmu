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
        Schema::table('distribusi', function (Blueprint $table) {
            // Pickup status flow: Menunggu → Sedang Dijemput → Dijemput
            $table->string('pickup_status')->default('Menunggu')->after('delivered_at');
            $table->timestamp('pickup_at')->nullable()->after('pickup_status');
            $table->string('pickup_photo_proof')->nullable()->after('pickup_at');
            $table->text('pickup_notes')->nullable()->after('pickup_photo_proof');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusi', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_status',
                'pickup_at', 
                'pickup_photo_proof',
                'pickup_notes',
            ]);
        });
    }
};
