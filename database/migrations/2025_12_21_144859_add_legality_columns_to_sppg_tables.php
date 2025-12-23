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
        Schema::table('sppg', function (Blueprint $table) {
            $table->date('tanggal_operasional_pertama')->nullable()->after('tanggal_mulai_sewa');
            $table->string('nomor_ba_verval')->nullable()->after('tanggal_operasional_pertama');
            $table->date('tanggal_ba_verval')->nullable()->after('nomor_ba_verval');
            $table->string('ba_verval_path')->nullable()->after('tanggal_ba_verval');
            $table->string('permohonan_pengusul_path')->nullable()->after('ba_verval_path');
        });

        Schema::table('sppg_user_roles', function (Blueprint $table) {
            $table->string('sk_path')->nullable()->after('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppg', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_operasional_pertama',
                'nomor_ba_verval',
                'tanggal_ba_verval',
                'ba_verval_path',
                'permohonan_pengusul_path'
            ]);
        });

        Schema::table('sppg_user_roles', function (Blueprint $table) {
            $table->dropColumn('sk_path');
        });
    }
};
