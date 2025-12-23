<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeederForEmptySppgs extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sppgs = \App\Models\Sppg::doesntHave('schools')->get();

        foreach ($sppgs as $sppg) {
            $this->command->info("Adding schools for SPPG: {$sppg->nama_sppg}");

            // Create 3 dummy schools/recipients for each SPPG
            for ($i = 1; $i <= 3; $i++) {
                \App\Models\School::create([
                    'sppg_id' => $sppg->id,
                    'nama_sekolah' => "SD Muhammadiyah {$sppg->city_code} 0{$i}", // Dummy Name
                    'alamat' => "Jl. K.H. Ahmad Dahlan No. {$i}, {$sppg->nama_sppg}",
                    'province_code' => $sppg->province_code ?? '33', // Default to Jateng if null
                    'city_code' => $sppg->city_code ?? '3311', // Default to Sukoharjo code if null
                    'district_code' => $sppg->district_code ?? '331101',
                    'village_code' => $sppg->village_code ?? '3311012001',
                    'default_porsi_besar' => rand(40, 150),
                    'default_porsi_kecil' => rand(10, 50),
                ]);
            }
        }
    }
}
