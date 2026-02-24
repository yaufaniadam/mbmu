<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LembagaPengusul;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class LembagaPengusulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate hanya jika Anda ingin memulai dari awal setiap seed
        // LembagaPengusul::truncate(); 
        // User::whereHas('roles', fn ($q) => $q->where('name', 'Pimpinan Lembaga Pengusul'))->delete();

        $filePath = database_path('seeders/data/lembaga_pengusul.csv');

        if (!file_exists($filePath)) {
            $this->command->error("File data lengkap tidak ditemukan di: " . $filePath);
            return;
        }

        // Ambil role 'Pimpinan Lembaga Pengusul' (pastikan sudah dibuat oleh RolePermissionSeeder)
        $pimpinanRole = Role::firstOrCreate(['name' => 'Pimpinan Lembaga Pengusul']);

        $file = fopen($filePath, 'r');
        if ($file === false) {
            $this->command->error("Gagal membuka file: " . $filePath);
            return;
        }

        // Baca header untuk pemetaan kolom dinamis
        $header = fgetcsv($file);
        $colMap = array_flip($header);

        // Pastikan kolom yang diperlukan ada
        $requiredCols = [
            'Nama Lembaga Pengusul', 'Alamat Lembaga Pengusul', 
            'Nama Pimpinan Lembaga Pengusul', 'Alamat Pimpinan Lembaga Pengusul', 
            'No HP Pimpinan Lembaga Pengusul',
            'Email Pimpinan Lembaga Pengusul'
        ];

        foreach ($requiredCols as $col) {
            if (!isset($colMap[$col])) {
                $this->command->error("Kolom yang dibutuhkan '$col' tidak ditemukan di file CSV.");
                fclose($file);
                return;
            }
        }

        $this->command->info('Memulai impor data Lembaga Pengusul dan Pimpinan...');

        // Loop melalui setiap baris data
        while (($row = fgetcsv($file)) !== false) {
            $namaLembaga = $row[$colMap['Nama Lembaga Pengusul']] ?? null;
            $alamatLembaga = $row[$colMap['Alamat Lembaga Pengusul']] ?? null;
            $namaPimpinan = $row[$colMap['Nama Pimpinan Lembaga Pengusul']] ?? null;
            $alamatPimpinan = $row[$colMap['Alamat Pimpinan Lembaga Pengusul']] ?? null;
            $nohpPimpinan = $row[$colMap['No HP Pimpinan Lembaga Pengusul']] ?? null;
            $emailPimpinan = $row[$colMap['Email Pimpinan Lembaga Pengusul']] ?? null;
            // $suratPermohonan = $row[$colMap['Lampirkan Surat Permohonan Pengusul']] ?? null;

            // Jika data kunci tidak ada, lewati baris ini
            if (empty($namaLembaga) || empty($emailPimpinan) || empty($namaPimpinan) || empty($nohpPimpinan)) {
                continue;
            }

            // 1. Buat atau Ambil User (Pimpinan)
            $pimpinanUser = User::firstOrCreate(
                ['email' => $emailPimpinan],
                [
                    'name' => $namaPimpinan,
                    'telepon' => $nohpPimpinan,
                    'alamat' => $alamatPimpinan,
                    'password' => 'mbm' . substr(preg_replace('/[^0-9]/', '', $nohpPimpinan), -4)
                ]
            );

            // 2. Berikan role Pimpinan
            $pimpinanUser->assignRole($pimpinanRole);

            // 3. Buat atau Ambil Lembaga Pengusul
            LembagaPengusul::firstOrCreate(
                ['nama_lembaga' => $namaLembaga],
                [
                    'alamat_lembaga' => $alamatLembaga,
                    'pimpinan_id' => $pimpinanUser->id
                ]
            );
        }

        fclose($file);
        
        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Impor Lembaga Pengusul dan Pimpinan berhasil.');
    }
}
