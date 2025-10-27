<?php

namespace Database\Seeders;

use App\Models\Sppg;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SppgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key untuk sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Kosongkan tabel sppg terlebih dahulu
        Sppg::truncate();

        $filePath = database_path('seeders/data/sppg_import.csv');

        // Periksa apakah file ada
        if (!file_exists($filePath)) {
            $this->command->error("File impor tidak ditemukan di: " . $filePath);
            return;
        }

        // Buka file CSV
        $file = fopen($filePath, 'r');

        if ($file === false) {
            $this->command->error("Gagal membuka file: " . $filePath);
            return;
        }

        // Lewati baris header (baris pertama)
        fgetcsv($file);

        $this->command->info('Memulai impor data SPPG dari file CSV...');

        // Loop melalui setiap baris di file CSV
        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            // $row[0] = ID SPPG
            // $row[1] = Nama SPPG
            // $row[2] = Alamat SPPG

            // Gunakan firstOrCreate untuk menghindari duplikasi jika seeder dijalankan lagi
            // Kita gunakan 'kode_sppg' sebagai kunci unik
            Sppg::firstOrCreate(
                [
                    'kode_sppg' => $row[0] ?? null,
                ],
                [
                    'nama_sppg' => $row[1] ?? 'Nama Tidak Diketahui',
                    'alamat' => $row[2] ?? 'Alamat Tidak Diketahui',
                    // Kolom lain (seperti 'kepala_sppg_id') akan null untuk saat ini
                    // Anda harus mengisinya secara manual atau melalui seeder lain nanti
                ]
            );
        }

        fclose($file);

        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Impor data SPPG berhasil diselesaikan.');
    }
}
