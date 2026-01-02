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
            $table->string('photo_path')->nullable()->after('longitude');
            $table->enum('grade', ['A', 'B', 'C'])->nullable()->after('photo_path');
           
            // Document paths
            $table->string('izin_operasional_path')->nullable()->after('grade');
            $table->string('sertifikat_halal_path')->nullable()->after('izin_operasional_path');
            $table->string('slhs_path')->nullable()->after('sertifikat_halal_path');
            $table->string('lhaccp_path')->nullable()->after('slhs_path');
            $table->string('iso_path')->nullable()->after('lhaccp_path');
            $table->string('sertifikat_lahan_path')->nullable()->after('iso_path');
            $table->string('dokumen_lain_path')->nullable()->after('sertifikat_lahan_path');
        });

        Schema::table('sekolah', function (Blueprint $table) {
            // Lat/Long might be nullable if not yet set
            $table->string('latitude')->nullable()->after('alamat');
            $table->string('longitude')->nullable()->after('latitude');
            $table->string('photo_path')->nullable()->after('longitude');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('gender');
        });

        Schema::table('relawan', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('gender');
            $table->string('photo_path')->nullable()->after('birth_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppg', function (Blueprint $table) {
            $table->dropColumn([
                'photo_path',
                'grade',
                'izin_operasional_path',
                'sertifikat_halal_path',
                'slhs_path',
                'lhaccp_path',
                'iso_path',
                'sertifikat_lahan_path',
                'dokumen_lain_path',
            ]);
        });

        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'photo_path']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('birth_date');
        });

        Schema::table('relawan', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'photo_path']);
        });
    }
};
