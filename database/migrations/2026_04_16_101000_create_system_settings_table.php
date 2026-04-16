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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default WhatsApp bulk message
        DB::table('system_settings')->insert([
            'key' => 'whatsapp_bulk_message',
            'value' => "📢 PENGISIAN DATA SPPG MUHAMMADIYAH DI SISTEM MBM1912\n\nAssalamu’alaikum warahmatullahi wabarakatuh,\n\nYth. Bapak/Ibu PJ Pelaksana SPPG Muhammadiyah,\n\nSehubungan dengan telah tersedianya Sistem Informasi Manajemen MBM melalui website:\n🌐 www.mbm1912.id\n\nKami mengimbau kepada seluruh PJ Pelaksana SPPG untuk segera:\n✅ Mengisi dan melengkapi profil SPPG\n✅ Menginput aktivitas/kegiatan SPPG\n✅ Mengunggah laporan kegiatan dan laporan keuangan\n✅ Melengkapi data pendukung lainnya pada sistem\n\n📖 Panduan penggunaan MBM1912 bagi PJ Pelaksana dapat diakses melalui link berikut:\nhttps://drive.google.com/drive/folders/1iVmSQ9hq4QzzmT6BGaUyxTut8Lq8YKzC?usp=sharing\n\nHal ini bertujuan untuk meningkatkan tertib administrasi, transparansi, dan monitoring program MBM secara nasional.\n\nMohon agar pengisian data dapat dilakukan secara berkala dan tepat waktu.\n\nDemikian disampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.\n\nWassalamu’alaikum warahmatullahi wabarakatuh.",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
