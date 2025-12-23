<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SppgLegacyImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks for safety during mass import
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $filePath = database_path('seeders/data/sppg_legacy_import.csv');

        if (!file_exists($filePath)) {
            $this->command->error("File import tidak ditemukan di: " . $filePath);
            return;
        }

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file, 0, ';'); // Use semicolon delimiter
        
        // Define clean mapping for internal use
        $colMap = array_flip($header);

        $this->command->info('Memulai impor data SPPG Legacy...');

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            try {
                // 1. Proses Lembaga Pengusul & Pimpinan
                $emailPimpinanLP = trim($row[$this->getCol($colMap, 'Email Pimpinan Lembaga Pengusul')] ?? '');
                if (empty($emailPimpinanLP) || $emailPimpinanLP === '.' || $emailPimpinanLP === '-') continue;

                $pimpinanLP = \App\Models\User::firstOrCreate(
                    ['email' => $emailPimpinanLP],
                    [
                        'name' => $row[$this->getCol($colMap, 'Nama Pimpinan Lembaga Pengusul')],
                        'telepon' => $row[$this->getCol($colMap, 'No HP Pimpinan Lembaga Pengusul')],
                        'alamat' => $row[$this->getCol($colMap, 'Alamat Pimpinan Lembaga Pengusul')],
                        'nik' => $row[$this->getCol($colMap, 'NIK Pimpinan Lembaga Pengusul')] ?? null,
                        'password' => \Illuminate\Support\Facades\Hash::make('p4$$w0rd')
                    ]
                );
                $pimpinanLP->assignRole('Pimpinan Lembaga Pengusul');

                $lembaga = \App\Models\LembagaPengusul::firstOrCreate(
                    ['nama_lembaga' => $row[$this->getCol($colMap, 'Nama Lembaga Pengusul')]],
                    [
                        'alamat_lembaga' => $row[$this->getCol($colMap, 'Alamat Lembaga Pengusul')],
                        'pimpinan_id' => $pimpinanLP->id
                    ]
                );

                // 2. Buat User Kepala SPPG
                $emailKepalaSppgCheck = $this->getCol($colMap, 'Email Kepala SPPG');
                $emailKepalaSppg = ($emailKepalaSppgCheck !== null) ? trim($row[$emailKepalaSppgCheck] ?? '') : null;
                $kepalaSppgUser = null;
                if (!empty($emailKepalaSppg) && $emailKepalaSppg !== '.' && $emailKepalaSppg !== '-') {
                    $kepalaSppgUser = \App\Models\User::firstOrCreate(
                        ['email' => $emailKepalaSppg],
                        [
                            'name' => $row[$this->getCol($colMap, 'Nama Kepala SPPG')],
                            'telepon' => $row[$this->getCol($colMap, 'No. HP Kepala SPPG')],
                            'alamat' => $row[$this->getCol($colMap, 'Alamat Kepala SPPG')],
                            'nik' => $row[$this->getCol($colMap, 'NIK Kepala SPPG')] ?? null,
                            'password' => \Illuminate\Support\Facades\Hash::make('p4$$w0rd')
                        ]
                    );
                    $kepalaSppgUser->assignRole('Kepala SPPG');
                }

                // 3. Buat/Update SPPG
                $sppg = \App\Models\Sppg::updateOrCreate(
                    ['kode_sppg' => $row[$this->getCol($colMap, 'ID SPPG')]],
                    [
                        'nama_sppg' => $row[$this->getCol($colMap, 'Nama SPPG')],
                        'alamat' => $row[$this->getCol($colMap, 'Alamat SPPG')],
                        'nama_bank' => $row[$this->getCol($colMap, 'Bank')], 
                        'nomor_va' => $row[$this->getCol($colMap, 'Nomor VA')], 
                        'lembaga_pengusul_id' => $lembaga->id,
                        'kepala_sppg_id' => $kepalaSppgUser?->id,
                        'tanggal_operasional_pertama' => $this->parseDate($row[$this->getCol($colMap, 'Tanggal Pertama Kali Operasional2')]),
                        'nomor_ba_verval' => ($colIndex = $this->getCol($colMap, 'Nomor BA Verval Terbit')) !== null ? $row[$colIndex] : null, 
                        'tanggal_ba_verval' => $this->parseDate($row[$this->getCol($colMap, 'Tanggal BA verval')]),
                        'ba_verval_path' => $row[$this->getCol($colMap, 'Lampirkan BA Verval')],
                        'permohonan_pengusul_path' => $row[$this->getCol($colMap, 'Lampirkan Surat  Permohonan Pengusul ')],
                    ]
                );

                // 4. Assign Roles & SK (Pivot)
                
                // SK Kepala SPPG
                if ($kepalaSppgUser) {
                    $skPath = ($colIndex = $this->getCol($colMap, 'SK Kepala SPPG')) !== null ? ($row[$colIndex] ?? null) : null;
                    $this->assignRoleWithSk($sppg, $kepalaSppgUser, 'Kepala SPPG', $skPath);
                }

                // Tenaga Ahli Gizi
                $this->processStaff($row, $colMap, $sppg, 'Tenaga Ahli Gizi', 'Staf Gizi');

                // Akuntan
                $this->processStaff($row, $colMap, $sppg, 'Akuntan', 'Staf Akuntan');

                // Penerima Kuasa
                $this->processStaff($row, $colMap, $sppg, 'Penerima Kuasa', 'Penerima Kuasa');

                // PJ Pelaksana
                $this->processStaff($row, $colMap, $sppg, 'Penanggungjawab Pelaksana', 'PJ Pelaksana');

            } catch (\Exception $e) {
                $this->command->error("Gagal mengimpor baris SPPG " . ($row[$this->getCol($colMap, 'Nama SPPG')] ?? '不明') . ": " . $e->getMessage());
            }
        }

        fclose($file);
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Impor data SPPG Legacy berhasil!');
    }

    private function processStaff($row, $colMap, $sppg, $prefix, $roleName)
    {
        $emailKey = "Email $prefix";
        $emailIndex = $this->getCol($colMap, $emailKey);
        
        $email = ($emailIndex !== null) ? trim($row[$emailIndex] ?? '') : null;
        if (empty($email) || $email === '.' || $email === '-') return;

        // Handle inconsistencies in client column naming
        $namaKey = ($prefix === 'Penanggungjawab Pelaksana') ? "Nama $prefix" : "Nama Lengkap $prefix";
        $hpKey = "No HP $prefix";
        $alamatKey = ($prefix === 'Penerima Kuasa' || $prefix === 'Penanggungjawab Pelaksana') ? "Alamat $prefix" : "Alamat Lengkap $prefix";
        $skKey = "Lampirkan SK $prefix";
        $nikKey = ($prefix === 'Akuntan') ? "NIKAkuntan" : "NIK $prefix";

        $user = \App\Models\User::firstOrCreate(
            ['email' => $email],
            [
                'name' => trim($row[$this->getCol($colMap, $namaKey)] ?? 'Staff'),
                'telepon' => trim($row[$this->getCol($colMap, $hpKey)] ?? null),
                'alamat' => trim($row[$this->getCol($colMap, $alamatKey)] ?? null),
                'nik' => $this->cleanNik($row[$this->getCol($colMap, $nikKey)] ?? null),
                'password' => \Illuminate\Support\Facades\Hash::make('p4$$w0rd')
            ]
        );
        $user->assignRole($roleName);

        $this->assignRoleWithSk($sppg, $user, $roleName, $row[$this->getCol($colMap, $skKey)] ?? null);
    }

    private function assignRoleWithSk($sppg, $user, $roleName, $skPath)
    {
        $role = \Spatie\Permission\Models\Role::findByName($roleName);
        
        \App\Models\SppgUserRole::updateOrCreate(
            [
                'sppg_id' => $sppg->id,
                'user_id' => $user->id,
                'role_id' => $role->id,
            ],
            [
                'sk_path' => trim($skPath ?? '')
            ]
        );
    }

    private function getCol($colMap, $name)
    {
        return $colMap[$name] ?? null;
    }

    private function cleanNik($nik)
    {
        if (empty($nik)) return null;
        // Remove dots, spaces, and other non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $nik);
        return $cleaned ?: null;
    }

    private function parseDate($dateString)
    {
        $dateString = trim($dateString);
        if (empty($dateString) || $dateString === '.' || $dateString === '-') return null;
        
        try {
            // Try DD/MM/YYYY first as it is the most common in the file
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $dateString)) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
