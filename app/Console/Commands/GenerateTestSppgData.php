<?php

namespace App\Console\Commands;

use App\Models\LembagaPengusul;
use App\Models\ProductionSchedule;
use App\Models\Sppg;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenerateTestSppgData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mbm:generate-test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 10 dummy SPPGs and Schedules for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting generation of test data...');

        // Fetch valid location data
        $village = \Laravolt\Indonesia\Models\Village::first();
        if (!$village) {
            $this->error('No location data found in database. Please seed Indonesia locations.');
            return;
        }
        $districtCode = $village->district_code;
        // Assuming standard Indonesia code format
        $cityCode = substr($districtCode, 0, 4);
        $provinceCode = substr($cityCode, 0, 2);

        $password = Hash::make('password'); // Default password for all users

        for ($i = 1; $i <= 10; $i++) {
            $this->info("Generating Set $i...");

            // 1. Create Users
            $kepalaSppg = User::firstOrCreate(
                ['email' => "kepala_sppg_$i@example.com"],
                [
                    'name' => "Kepala SPPG $i",
                    'password' => $password,
                    'telepon' => '08123456789' . $i,
                    'alamat' => 'Alamat Kepala ' . $i,
                ]
            );
            // Assign role if possible (assuming Spatie Permission)
            try {
                if (!$kepalaSppg->hasRole('Kepala SPPG')) {
                    $kepalaSppg->assignRole('Kepala SPPG');
                }
            } catch (\Exception $e) {
                $this->warn("Role 'Kepala SPPG' not found or could not be assigned.");
            }

            $pjSppg = User::firstOrCreate(
                ['email' => "pj_sppg_$i@example.com"],
                [
                    'name' => "PJ SPPG $i",
                    'password' => $password,
                    'telepon' => '08129876543' . $i,
                    'alamat' => 'Alamat PJ ' . $i,
                ]
            );
            try {
                if (!$pjSppg->hasRole('PJ Pelaksana')) {
                    $pjSppg->assignRole('PJ Pelaksana');
                }
            } catch (\Exception $e) {
                // Ignore
            }

            $pimpinanLembaga = User::firstOrCreate(
                ['email' => "pimpinan_lembaga_$i@example.com"],
                [
                    'name' => "Pimpinan Lembaga $i",
                    'password' => $password,
                    'telepon' => '08121112223' . $i,
                    'alamat' => 'Alamat Pimpinan ' . $i,
                ]
            );
            try {
                if (!$pimpinanLembaga->hasRole('Pimpinan Lembaga Pengusul')) {
                    $pimpinanLembaga->assignRole('Pimpinan Lembaga Pengusul');
                }
            } catch (\Exception $e) {
                // Ignore
            }

            // 2. Create Lembaga Pengusul
            $lembaga = LembagaPengusul::firstOrCreate(
                ['nama_lembaga' => "Lembaga Pengusul Test $i"],
                [
                    'alamat_lembaga' => "Alamat Lembaga $i",
                    'pimpinan_id' => $pimpinanLembaga->id,
                ]
            );

            // 3. Create SPPG
            $sppg = Sppg::firstOrCreate(
                ['kode_sppg' => "SPPG-TEST-" . sprintf('%03d', $i)],
                [
                    'nama_sppg' => "SPPG Test Unit $i",
                    'nama_bank' => 'Bank Test',
                    'nomor_va' => '88889999000' . $i,
                    'balance' => 0,
                    'alamat' => "Jl. SPPG Test No. $i",
                    'is_active' => true,
                    'status' => 'Aktif',
                    'tanggal_mulai_sewa' => now()->subMonths(rand(1, 12)),
                    'kepala_sppg_id' => $kepalaSppg->id,
                    'pj_id' => $pjSppg->id,
                    'lembaga_pengusul_id' => $lembaga->id,
                    'province_code' => $provinceCode,
                    'city_code' => $cityCode,
                    'district_code' => $districtCode,
                    'village_code' => $village->code,
                    'grade' => ['A', 'B', 'C'][rand(0, 2)],
                ]
            );

            // Update user's sppg_id for simple relations if needed
             $kepalaSppg->update(['sppg_id' => $sppg->id]);
             $pjSppg->update(['sppg_id' => $sppg->id]);
             
             // Attach to pivot table for 'staff' relationship
             // Assuming role_id is needed in pivot, but SppgUserRole model might handle it or it's just a linker.
             // Checking SppgUserRole model might be good, but standard belongsToMany attach works.
             // If pivot has extra columns like 'role_id', we should try to fill them if we know them.
             // For now, attaching without pivot data or with role_id if available.
             // Checking Sppg model: ->withPivot(['role_id', 'sk_path'])
             
             // We need to know what role_id to put. 
             // Let's assume we can fetch the Role model id.
             $kepalaRole = \Spatie\Permission\Models\Role::where('name', 'Kepala SPPG')->first();
             $pjRole = \Spatie\Permission\Models\Role::where('name', 'PJ Pelaksana')->first();
             $giziRole = \Spatie\Permission\Models\Role::where('name', 'Staf Gizi')->first();
             $akuntanRole = \Spatie\Permission\Models\Role::where('name', 'Staf Akuntan')->first();
             $pengantaranRole = \Spatie\Permission\Models\Role::where('name', 'Staf Pengantaran')->first();
             
             // Create other staff
             $giziUser = User::firstOrCreate(
                 ['email' => "gizi_$i@example.com"],
                 [
                     'name' => "Staf Gizi $i",
                     'password' => $password,
                     'telepon' => '08130000001' . $i,
                     'alamat' => 'Alamat Gizi ' . $i,
                 ]
             );
             if (!$giziUser->hasRole('Staf Gizi')) {
                 $giziUser->assignRole('Staf Gizi');
             }

             $akuntanUser = User::firstOrCreate(
                 ['email' => "akuntan_$i@example.com"],
                 [
                     'name' => "Staf Akuntan $i",
                     'password' => $password,
                     'telepon' => '08130000002' . $i,
                     'alamat' => 'Alamat Akuntan ' . $i,
                 ]
             );
             if (!$akuntanUser->hasRole('Staf Akuntan')) {
                 $akuntanUser->assignRole('Staf Akuntan');
             }

             $pengantaranUser = User::firstOrCreate(
                 ['email' => "pengantaran_$i@example.com"],
                 [
                     'name' => "Staf Pengantaran $i",
                     'password' => $password,
                     'telepon' => '08130000003' . $i,
                     'alamat' => 'Alamat Pengantaran ' . $i,
                 ]
             );
             if (!$pengantaranUser->hasRole('Staf Pengantaran')) {
                 $pengantaranUser->assignRole('Staf Pengantaran');
             }
             
             $sppg->staff()->syncWithoutDetaching([
                 $kepalaSppg->id => ['role_id' => $kepalaRole?->id],
                 $pjSppg->id => ['role_id' => $pjRole?->id],
                 $giziUser->id => ['role_id' => $giziRole?->id],
                 $akuntanUser->id => ['role_id' => $akuntanRole?->id],
                 $pengantaranUser->id => ['role_id' => $pengantaranRole?->id],
             ]);

             // 3b. Create Volunteers
             $positions = [
                 'Masak' => 'relawan_masak',
                 'Pengantaran' => 'relawan_pengantaran',
                 'Cuci' => 'relawan_cuci',
                 'Koordinator Lapangan' => 'koordinator_lapangan',
             ];

             foreach ($positions as $posisiName => $slug) {
                 \App\Models\Volunteer::firstOrCreate(
                     [
                         'sppg_id' => $sppg->id,
                         'posisi' => $posisiName, // Using the nice name for Posisi
                         'nama_relawan' => ucwords(str_replace('_', ' ', $slug)) . " $i",
                     ],
                     [
                         'nik' => '340000000000' . rand(1000, 9999),
                         'gender' => ['L', 'P'][rand(0, 1)],
                         'category' => 'Umum',
                         'kontak' => '0812' . rand(10000000, 99999999),
                         'address' => "Alamat Relawan $slug $i",
                         'daily_rate' => 50000,
                     ]
                 );
             }

             // 3c. Create Schools (10 Recipients)
             for ($s = 1; $s <= 10; $s++) {
                \App\Models\School::firstOrCreate(
                    [
                        'sppg_id' => $sppg->id,
                        'nama_sekolah' => "Sekolah {$i}_{$s}",
                    ],
                    [
                        'kategori' => 'Sekolah',
                        'alamat' => "Alamat Sekolah {$i}_{$s}",
                        'province_code' => $provinceCode,
                        'city_code' => $cityCode,
                        'district_code' => $districtCode,
                        'village_code' => $village->code,
                        'default_porsi_besar' => rand(10, 50),
                        'default_porsi_kecil' => rand(10, 50),
                    ]
                );
             }

            // 4. Create Production Schedule (Active/Completed)
            // Clear existing schedules to ensure we have exactly 10 matching the new criteria
            $sppg->productionSchedules()->delete();

            // Generate 10 schedules, going backwards from today, skipping Sundays
            $schedulesCreated = 0;
            $dateIterator = now();

            while ($schedulesCreated < 10) {
                // Skip if Sunday (0 = Sunday in Carbon/PHP w/ default settings usually, or isSunday())
                if ($dateIterator->isSunday()) {
                    $dateIterator->subDay();
                    continue;
                }

                \App\Models\ProductionSchedule::firstOrCreate(
                    [
                        'sppg_id' => $sppg->id,
                        'tanggal' => $dateIterator->format('Y-m-d'),
                    ],
                    [
                        'menu_hari_ini' => "Menu Tanggal " . $dateIterator->format('d M Y'),
                        'jumlah' => rand(100, 300),
                        'status' => 'Selesai',
                    ]
                );

                $schedulesCreated++;
                $dateIterator->subDay();
            }

        }

        $this->info('Successfully generated 10 sets of SPPG and Schedule data.');
        $this->info('');
        $this->info('LOGIN CREDENTIALS:');
        $this->info('All accounts have password: "password"');
        $this->info('Format Email: [role]_[number]@example.com');
        $this->info('-------------------------------------------');
        $this->info('Kepala SPPG      : kepala_sppg_1@example.com ... kepala_sppg_10@example.com');
        $this->info('PJ Pelaksana     : pj_sppg_1@example.com ... pj_sppg_10@example.com');
        $this->info('Staf Gizi        : gizi_1@example.com ... gizi_10@example.com');
        $this->info('Staf Akuntan     : akuntan_1@example.com ... akuntan_10@example.com');
        $this->info('Staf Pengantaran : pengantaran_1@example.com ... pengantaran_10@example.com');
        $this->info('Pimpinan Lembaga : pimpinan_lembaga_1@example.com ... pimpinan_lembaga_10@example.com');
    }
}
