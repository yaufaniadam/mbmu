<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'credential_pimpinan',
                'name' => 'Kridensial Login Pimpinan Lembaga',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "Berikut adalah akses login Anda untuk aplikasi Makan Bergizi Muhammadiyah (MBMu) sebagai Kepala Lembaga Pengusul.\n\n"
                    . "🔐 *Username*: {{username}}\n"
                    . "🔑 *Password*: {{password}}\n\n"
                    . "Silakan login melalui link berikut:\n"
                    . "👉 https://makanbergizimuhammadiyah.id/admin/login\n\n"
                    . "Langkah selanjutnya yang perlu dilakukan:\n"
                    . "1. Login ke aplikasi.\n"
                    . "2. Lengkapi Profil Lembaga Pengusul:\n"
                    . "   a. Nama Lembaga\n"
                    . "   b. Alamat\n"
                    . "   c. Nama Pimpinan Lembaga\n"
                    . "   d. Upload Dokumen Lembaga (Surat Permohonan, Surat Penunjukan, SK PJ Pelaksana, Dokumen Kerjasama).\n"
                    . "3. Mengirimkan Kontribusi Kornas.\n\n"
                    . "Terima Kasih.",
                'placeholders' => ['{{name}}', '{{username}}', '{{password}}'],
            ],
            [
                'key' => 'credential_sppg',
                'name' => 'Kridensial Login Kepala SPPG',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "Berikut adalah akses login Anda untuk aplikasi Makan Bergizi Muhammadiyah (MBMu) sebagai Kepala SPPG.\n\n"
                    . "🔐 *Username*: {{username}}\n"
                    . "🔑 *Password*: {{password}}\n\n"
                    . "Silakan login melalui link berikut:\n"
                    . "👉 https://makanbergizimuhammadiyah.id/admin/login\n\n"
                    . "Instruksi selanjutnya:\n"
                    . "1. Login ke sistem menggunakan username dan password yang disediakan.\n"
                    . "2. Melengkapi profil SPPG.\n"
                    . "3. Membuat akun untuk staf (Akuntan, Ahli Gizi, Pengantaran).\n"
                    . "4. Menambahkan Data Penerima Manfaat.\n"
                    . "5. Menambahkan Data Relawan.\n"
                    . "6. Mengisi Kegiatan Harian:\n"
                    . "   a. Aktifitas Dapur\n"
                    . "   b. Menu Makanan\n"
                    . "7. Laporan Keuangan.\n"
                    . "8. Laporan Pelaksanaan.\n"
                    . "9. Mengisi Blog.\n"
                    . "10. Membayarkan Insentif ke Lembaga Pengusul.\n\n"
                    . "Terima Kasih.",
                'placeholders' => ['{{name}}', '{{username}}', '{{password}}'],
            ],
            [
                'key' => 'registration_token',
                'name' => 'Token Registrasi',
                'content' => "Assalamualaikum {{recipient_name}},\n\n"
                    . "Silakan login ke MBMu App lalu buat akun.\n\n"
                    . "👉 Link: {{registration_url}}\n"
                    . "🔑 Token: {{token}}\n\n"
                    . "Gunakan link dan token di atas untuk mendaftar sebagai {{role_label}} di {{sppg_name}}.\n\n"
                    . "Terima Kasih.",
                'placeholders' => ['{{recipient_name}}', '{{registration_url}}', '{{token}}', '{{role_label}}', '{{sppg_name}}'],
            ],
            [
                'key' => 'contribution_payment_received',
                'name' => 'Pembayaran Kontribusi Masuk',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "Ada pembayaran Kontribusi masuk dari *{{institution_name}}* ({{sppg_name}}).\n\n"
                    . "📄 *Invoice*: {{invoice_number}}\n"
                    . "💰 *Jumlah*: {{amount}}\n\n"
                    . "Mohon segera lakukan verifikasi pembayaran di panel admin.\n\n"
                    . "Terima kasih.",
                'placeholders' => ['{{name}}', '{{institution_name}}', '{{sppg_name}}', '{{invoice_number}}', '{{amount}}'],
            ],
            [
                'key' => 'invoice_generated',
                'name' => 'Invoice Baru Diterbitkan',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "Invoice Baru telah diterbitkan untuk {{sppg_name}}.\n\n"
                    . "📄 *Invoice*: {{invoice_number}}\n"
                    . "🗓 *Periode*: {{period}}\n"
                    . "💰 *Total*: {{amount}}\n\n"
                    . "Mohon segera lakukan pembayaran ke rekening berikut:\n"
                    . "🏦 *Bank*: {{bank_name}}\n"
                    . "💳 *No. Rek*: {{account_number}}\n"
                    . "An. {{account_holder}}\n\n"
                    . "Terima kasih.",
                'placeholders' => ['{{name}}', '{{sppg_name}}', '{{invoice_number}}', '{{period}}', '{{amount}}', '{{bank_name}}', '{{account_number}}', '{{account_holder}}'],
            ],
            [
                'key' => 'instruction_published',
                'name' => 'Instruksi Baru Diterbitkan',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "📢 *INSTRUKSI BARU*\n\n"
                    . "*{{title}}*\n\n"
                    . "{{snippet}}\n\n"
                    . "Silakan baca selengkapnya di aplikasi.\n\n"
                    . "Terima kasih.",
                'placeholders' => ['{{name}}', '{{title}}', '{{snippet}}'],
            ],
            [
                'key' => 'sppg_incentive_payment',
                'name' => 'Pembayaran Insentif SPPG Masuk',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "Ada konfirmasi pembayaran Insentif dari *{{sppg_name}}*.\n\n"
                    . "📄 *Invoice*: {{invoice_number}}\n"
                    . "💰 *Jumlah*: {{amount}}\n"
                    . "🏦 *Bank*: {{source_bank}} ({{transfer_date}})\n\n"
                    . "Mohon segera verifikasi di Panel Admin Lembaga.\n\n"
                    . "Terima kasih.",
                'placeholders' => ['{{name}}', '{{sppg_name}}', '{{invoice_number}}', '{{amount}}', '{{source_bank}}', '{{transfer_date}}'],
            ],
            [
                'key' => 'invoice_approved',
                'name' => 'Pembayaran Invoice Disetujui',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "✅ *PEMBAYARAN DITERIMA*\n\n"
                    . "Invoice *{{invoice_number}}* telah diverifikasi oleh Lembaga Pengusul.\n\n"
                    . "Terima kasih atas kerja samanya.",
                'placeholders' => ['{{name}}', '{{invoice_number}}'],
            ],
            [
                'key' => 'invoice_rejected',
                'name' => 'Pembayaran Invoice Ditolak',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "❌ *PEMBAYARAN DITOLAK*\n\n"
                    . "Maaf, pembayaran untuk Invoice *{{invoice_number}}* ditolak.\n\n"
                    . "📝 *Alasan*: {{reason}}\n\n"
                    . "Mohon perbaiki data pembayaran dan upload ulang bukti transfer di Panel Admin.",
                'placeholders' => ['{{name}}', '{{invoice_number}}', '{{reason}}'],
            ],
            [
                'key' => 'royalty_payment_due',
                'name' => 'Tagihan Kontribusi Terbit',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "📢 *TAGIHAN KONTRIBUSI*\n\n"
                    . "Tagihan Kontribusi 10% untuk periode ini telah terbit secara otomatis.\n\n"
                    . "📄 *No Invoice*: {{invoice_number}}\n"
                    . "💰 *Jumlah*: {{amount}}\n"
                    . "🗓️ *Jatuh Tempo*: {{due_date}}\n\n"
                    . "Mohon segera lakukan pembayaran melalui menu *Bayar Kontribusi* di aplikasi.\n\n"
                    . "Terima kasih.",
                'placeholders' => ['{{name}}', '{{invoice_number}}', '{{amount}}', '{{due_date}}'],
            ],
            [
                'key' => 'royalty_payment_submitted',
                'name' => 'Pembayaran Kontribusi Masuk',
                'content' => "Assalamualaikum Admin Kornas,\n\n"
                    . "💰 *PEMBAYARAN KONTRIBUSI*\n\n"
                    . "Ada pembayaran kontribusi masuk dari *{{lp_name}}*.\n\n"
                    . "📄 *Invoice*: {{invoice_number}}\n"
                    . "💰 *Jumlah*: {{amount}}\n"
                    . "🏦 *Bank*: {{source_bank}}\n\n"
                    . "Mohon segera verifikasi di Panel Admin Kornas.\n\n"
                    . "Terima kasih.",
                'placeholders' => ['{{lp_name}}', '{{invoice_number}}', '{{amount}}', '{{source_bank}}'],
            ],
            [
                'key' => 'royalty_approved',
                'name' => 'Pembayaran Kontribusi Diterima',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "✅ *PEMBAYARAN DITERIMA*\n\n"
                    . "Pembayaran Kontribusi Kornas untuk Invoice *{{invoice_number}}* telah diterima dan diverifikasi.\n\n"
                    . "Terima kasih atas kontribusi Anda untuk kemajuan bersama.",
                'placeholders' => ['{{name}}', '{{invoice_number}}'],
            ],
            [
                'key' => 'royalty_rejected',
                'name' => 'Pembayaran Kontribusi Ditolak',
                'content' => "Assalamualaikum {{name}},\n\n"
                    . "❌ *PEMBAYARAN DITOLAK*\n\n"
                    . "Maaf, pembayaran Kontribusi Kornas untuk Invoice *{{invoice_number}}* ditolak.\n\n"
                    . "📝 *Alasan*: {{reason}}\n\n"
                    . "Mohon perbaiki data pembayaran dan upload ulang bukti transfer di Panel Admin.",
                'placeholders' => ['{{name}}', '{{invoice_number}}', '{{reason}}'],
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
