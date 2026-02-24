<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Sppg;
use App\Models\ProductionSchedule;
use App\Models\School;
use App\Models\Distribution;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DeliveryTestSeeder extends Seeder
{
    public function run()
    {
        try {
            // Ensure Role Exists
            if (!Role::where('name', 'Staf Pengantaran')->exists()) {
                Role::create(['name' => 'Staf Pengantaran']);
            }

            // Create User
            $user = User::firstOrCreate(
                ['email' => 'driver@mbmu.id'],
                [
                    'name' => 'Driver Test',
                    'password' => 'password',
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('Staf Pengantaran');

            $this->command->info('User created: driver@mbmu.id / password');

            // Create Dependencies
            // Manual Create School (No Factory)
            $sppg = Sppg::first();
            if (!$sppg) {
                // Manual create because Factory might be broken/missing
                $sppg = new Sppg();
                $sppg->nama_sppg = 'SPPG Test Unit';
                $sppg->kode_sppg = 'SPPG-' . Str::random(5);
                $sppg->alamat = 'Jl. Contoh Alamat SPPG';
                $sppg->save();
            }

            $school = School::firstOrCreate(
                ['nama_sekolah' => 'SD Muhammadiyah 1'],
                [
                    'sppg_id' => $sppg->id,
                    'alamat' => 'Jl. Test No. 1',
                    'kategori' => 'Sekolah',
                ]
            );

            // Create Schedule
            $schedule = ProductionSchedule::firstOrCreate(
                [
                    'sppg_id' => $sppg->id,
                    'tanggal' => Carbon::today(),
                ],
                [
                    'menu_hari_ini' => 'Ayam Goreng + Sayur Asem',
                    'jumlah' => 500,
                    'status' => 'Didistribusikan',
                ]
            );

            // Create Distributions (Tasks)
            
            // 1. Pending Delivery
            Distribution::create([
                'user_id' => $user->id,
                'jadwal_produksi_id' => $schedule->id,
                'sekolah_id' => $school->id,
                'jumlah_porsi_besar' => 50,
                'jumlah_porsi_kecil' => 10,
                'status_pengantaran' => 'Sedang Dikirim',
            ]);

            // 2. Completed Delivery (Pending Pickup)
            $distPickup = Distribution::create([
                'user_id' => $user->id,
                'jadwal_produksi_id' => $schedule->id,
                'sekolah_id' => $school->id,
                'jumlah_porsi_besar' => 30,
                'jumlah_porsi_kecil' => 5,
                'status_pengantaran' => 'Terkirim',
                'delivered_at' => now()->subHours(2),
            ]);

            $this->command->info('Test data created successfully.');
        } catch (\Exception $e) {
            $this->command->error('Seeder Failed: ' . $e->getMessage());
        }
    }
}
