<?php

namespace Database\Seeders;

use App\Models\Sppg;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Permissions (Izin Aksi)
        $permissions = [
            // Dashboard
            'view-national-dashboard',
            'view-sppg-dashboard',

            // Manajemen Data Master (oleh Kornas)
            'manage-sppg',
            'manage-all-users',
            'manage-roles',

            // Manajemen Data SPPG (oleh Kepala SPPG)
            'manage-sppg-users',
            'manage-sppg-mitra',
            'manage-sppg-sekolah',
            'manage-sppg-relawan',

            // Aktivitas Harian SPPG
            'manage-jadwal-produksi',
            'perform-verifikasi-pangan', // Khusus Staf Gizi
            'confirm-distribusi',      // Khusus Staf Pengantaran

            // Laporan & Keuangan
            'view-national-reports',
            'view-sppg-reports',
            'manage-sppg-finance',
            'confirm-kornas-deposit', // Khusus Staf Kornas
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Buat Roles (Peran Pengguna)
        $superadminRole = Role::firstOrCreate(['name' => 'Superadmin']);
        $direkturKornasRole = Role::firstOrCreate(['name' => 'Direktur Kornas']);
        $stafKornasRole = Role::firstOrCreate(['name' => 'Staf Kornas']);
        $kepalaSppgRole = Role::firstOrCreate(['name' => 'Kepala SPPG']);
        $stafAdminSppgRole = Role::firstOrCreate(['name' => 'Staf Administrator SPPG']);
        $stafGiziRole = Role::firstOrCreate(['name' => 'Staf Gizi']);
        $stafPengantaranRole = Role::firstOrCreate(['name' => 'Staf Pengantaran']);
        $stafAkuntanRole = Role::firstOrCreate(['name' => 'Staf Akuntan']);
        // === PERAN BARU ===
        $pjPelaksanaRole = Role::firstOrCreate(['name' => 'PJ Pelaksana']);
        $penerimaKuasaRole = Role::firstOrCreate(['name' => 'Penerima Kuasa']);
        $pimpinanPengusulRole = Role::firstOrCreate(['name' => 'Pimpinan Lembaga Pengusul']);

        // 4. Berikan Permissions ke Roles
        // Superadmin mendapatkan semua akses via AuthServiceProvider

        // Direktur Kornas
        $direkturKornasRole->syncPermissions(['view-national-dashboard', 'view-national-reports']);

        // Staf Kornas
        $stafKornasRole->syncPermissions(['view-national-dashboard', 'view-national-reports', 'manage-sppg', 'manage-all-users', 'confirm-kornas-deposit']);

        // Kepala SPPG
        $kepalaSppgRole->syncPermissions(['view-sppg-dashboard', 'manage-sppg-users', 'manage-sppg-mitra', 'manage-sppg-sekolah', 'manage-sppg-relawan', 'manage-jadwal-produksi', 'view-sppg-reports', 'manage-sppg-finance']);

        // Staf Administrator SPPG
        $stafAdminSppgRole->syncPermissions(['view-sppg-dashboard', 'manage-jadwal-produksi']);

        // Staf Gizi
        $stafGiziRole->syncPermissions(['perform-verifikasi-pangan']);

        // Staf Pengantaran
        $stafPengantaranRole->syncPermissions(['confirm-distribusi']);

        // Staf Akuntan
        $stafAkuntanRole->syncPermissions(['view-sppg-reports', 'manage-sppg-finance']);

        // === IZIN UNTUK PERAN BARU ===
        // PJ Pelaksana: Hanya bisa melihat dashboard dan laporan SPPG terkait
        $pjPelaksanaRole->syncPermissions(['view-sppg-dashboard', 'view-sppg-reports']);

        // Penerima Kuasa: Sama seperti PJ Pelaksana, hanya bisa melihat
        $penerimaKuasaRole->syncPermissions(['view-sppg-dashboard', 'view-sppg-reports']);

        $pimpinanPengusulRole->syncPermissions(['view-sppg-dashboard', 'view-sppg-reports']);


        // 5. Buat Users Bawaan
        // User Level Nasional (tidak terikat SPPG)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@mbm.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('p4$$w0rd')]
        );
        $superAdmin->assignRole($superadminRole);

        $direktur = User::firstOrCreate(
            ['email' => 'direktur.kornas@mbm.com'],
            ['name' => 'Direktur Kornas MBM', 'password' => Hash::make('p4$$w0rd')]
        );
        $direktur->assignRole($direkturKornasRole);

        $stafKornas = User::firstOrCreate(
            ['email' => 'staf.kornas@mbm.com'],
            ['name' => 'Staf Kornas MBM', 'password' => Hash::make('p4$$w0rd')]
        );
        $stafKornas->assignRole($stafKornasRole);
        

        // User Level SPPG (terikat pada SPPG pertama yang ada di database)
        $firstSppg = Sppg::first();
        if ($firstSppg) {
            // Kepala SPPG (Contoh: Menjadi kepala di SPPG pertama)
            $kepalaSppg = User::firstOrCreate(
                ['email' => 'kepala.sppg.pcmbaki@mbm.com'],
                ['name' => 'Kepala SPPG PCM BAKI', 'password' => Hash::make('p4$$w0rd')]
            );
            // Memberikan peran dalam konteks SPPG spesifik
            $kepalaSppg->assignRole($kepalaSppgRole->name, $firstSppg->id);

            // Staf-staf lain di SPPG pertama
            $adminSppg = User::firstOrCreate(
                ['email' => 'admin.sppg.pcmbaki@mbm.com'],
                ['name' => 'Admin SPPG PCM BAKI', 'password' => Hash::make('p4$$w0rd')]
            );
            $adminSppg->assignRole($stafAdminSppgRole->name, $firstSppg->id);

            $giziSppg = User::firstOrCreate(
                ['email' => 'gizi.sppg.pcmbaki@mbm.com'],
                ['name' => 'Gizi SPPG PCM BAKI', 'password' => Hash::make('p4$$w0rd')]
            );
            $giziSppg->assignRole($stafGiziRole->name, $firstSppg->id);

            $kurirSppg = User::firstOrCreate(
                ['email' => 'kurir.sppg.pcmbaki@mbm.com'],
                ['name' => 'Kurir SPPG PCM BAKI', 'password' => Hash::make('p4$$w0rd')]
            );
            $kurirSppg->assignRole($stafPengantaranRole->name, $firstSppg->id);

            $akuntanSppg = User::firstOrCreate(
                ['email' => 'akuntan.sppg.pcmbaki@mbm.com'],
                ['name' => 'Akuntan SPPG PCM BAKI', 'password' => Hash::make('p4$$w0rd')]
            );
            $akuntanSppg->assignRole($stafAkuntanRole->name, $firstSppg->id);

            // === CONTOH USER UNTUK PERAN BARU ===
            $pjPelaksana = User::firstOrCreate(
                ['email' => 'pj.sppg.pcmbaki@mbm.com'],
                ['name' => 'PJ Pelaksana SPPG PCM BAKI', 'password' => Hash::make('p4$$w0rd')]
            );
            $pjPelaksana->assignRole($pjPelaksanaRole->name, $firstSppg->id);

            $penerimaKuasa = User::firstOrCreate(
                ['email' => 'kuasa.sppg.pcmbaki@mbm.com'],
                ['name' => 'Penerima Kuasa SPPG PCM BAKI', 'password' => Hash::make('p4$$w0rd')]
            );
            $penerimaKuasa->assignRole($penerimaKuasaRole->name, $firstSppg->id);
        }
    }
}