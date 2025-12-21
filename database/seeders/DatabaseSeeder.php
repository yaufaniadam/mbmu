<?php

namespace Database\Seeders;

use App\Models\LembagaPengusul;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\VillagesSeeder;
use Laravolt\Indonesia\Seeds\DistrictsSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call([
            ProvincesSeeder::class,
            CitiesSeeder::class,
            DistrictsSeeder::class,
            VillagesSeeder::class,
            SppgSeeder::class,
            ProductionVerificationSettingSeeder::class, // Added this line
            RolePermissionSeeder::class,
            LembagaPengusulSeeder::class,
         ]);

         // Post-Seeding: Link Kepala SPPG Users to Sppg Records
         $sppgs = \App\Models\Sppg::all();
         $kepalaSppgUsers = \App\Models\User::role('Kepala SPPG')->get();

         foreach ($sppgs as $index => $sppg) {
             if (isset($kepalaSppgUsers[$index])) {
                 $sppg->update(['kepala_sppg_id' => $kepalaSppgUsers[$index]->id]);
             }
         }

    }
}
