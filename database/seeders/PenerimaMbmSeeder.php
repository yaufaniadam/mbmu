<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Sppg;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenerimaMbmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/data/penerima_mbm.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $this->command->info('Importing Penerima MBM from CSV...');

        $file = fopen($csvFile, 'r');
        
        // Skip header row
        $header = fgetcsv($file);
        
        $imported = 0;
        $skipped = 0;
        
        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($file)) !== false) {
                // Map CSV columns to array keys
                $data = array_combine($header, $row);
                
                // Validate SPPG exists
                $sppg = Sppg::find($data['sppg_id']);
                if (!$sppg) {
                    $this->command->warn("SPPG ID {$data['sppg_id']} not found. Skipping: {$data['nama_sekolah']}");
                    $skipped++;
                    continue;
                }
                
                // Check if school already exists to avoid duplicates
                $exists = School::where('nama_sekolah', $data['nama_sekolah'])
                    ->where('sppg_id', $data['sppg_id'])
                    ->exists();
                    
                if ($exists) {
                    $this->command->warn("School already exists. Skipping: {$data['nama_sekolah']}");
                    $skipped++;
                    continue;
                }
                
                // Create school record - inherit region codes from SPPG
                School::create([
                    'sppg_id' => $data['sppg_id'],
                    'nama_sekolah' => $data['nama_sekolah'],
                    'kategori' => $data['kategori'] ?? 'Sekolah',
                    'alamat' => $data['alamat'] ?? '-',
                    'province_code' => $sppg->province_code,
                    'city_code' => $sppg->city_code,
                    'district_code' => $sppg->district_code,
                    'village_code' => $sppg->village_code,
                    'default_porsi_besar' => !empty($data['default_porsi_besar']) ? (int)$data['default_porsi_besar'] : 0,
                    'default_porsi_kecil' => !empty($data['default_porsi_kecil']) ? (int)$data['default_porsi_kecil'] : 0,
                ]);
                
                $imported++;
                $this->command->info("✓ Imported: {$data['nama_sekolah']} (SPPG: {$sppg->nama_sppg})");
            }
            
            DB::commit();
            
            $this->command->info("\n========================================");
            $this->command->info("Import Summary:");
            $this->command->info("✓ Successfully imported: {$imported} schools");
            if ($skipped > 0) {
                $this->command->warn("⚠ Skipped: {$skipped} schools");
            }
            $this->command->info("========================================\n");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error importing data: {$e->getMessage()}");
            throw $e;
        } finally {
            fclose($file);
        }
    }
}
