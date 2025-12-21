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
            'manage-lembaga-pengusul',

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
        $roles = [
            'Superadmin',
            'Direktur Kornas',
            'Staf Kornas',
            'Pimpinan Lembaga Pengusul',
            'Kepala SPPG',
            'PJ Pelaksana',
            'Penerima Kuasa',
            'Staf Administrator SPPG',
            'Staf Gizi',
            'Staf Akuntan',
            'Staf Akuntan Kornas',
            'Staf Pengantaran',
        ];

        $roleModels = [];
        foreach ($roles as $roleName) {
            $roleModels[$roleName] = Role::firstOrCreate(['name' => $roleName]);
        }

        // 4. Berikan Permissions ke Roles
        // Direktur Kornas
        $roleModels['Direktur Kornas']->syncPermissions(['view-national-dashboard', 'view-national-reports']);

        // Staf Kornas
        $roleModels['Staf Kornas']->syncPermissions([
            'view-national-dashboard',
            'view-national-reports',
            'manage-sppg',
            'manage-all-users',
            'manage-lembaga-pengusul',
            'confirm-kornas-deposit'
        ]);

        // Pimpinan Lembaga Pengusul
        $roleModels['Pimpinan Lembaga Pengusul']->syncPermissions(['view-sppg-dashboard', 'view-sppg-reports']);

        // Kepala SPPG
        $roleModels['Kepala SPPG']->syncPermissions([
            'view-sppg-dashboard',
            'manage-sppg-users',
            'manage-sppg-mitra',
            'manage-sppg-sekolah',
            'manage-sppg-relawan',
            'manage-jadwal-produksi',
            'view-sppg-reports',
            'manage-sppg-finance'
        ]);

        // PJ Pelaksana
        $roleModels['PJ Pelaksana']->syncPermissions(['view-sppg-dashboard', 'view-sppg-reports']);

        // Penerima Kuasa
        $roleModels['Penerima Kuasa']->syncPermissions(['view-sppg-dashboard', 'view-sppg-reports']);

        // Staf Administrator SPPG
        $roleModels['Staf Administrator SPPG']->syncPermissions(['view-sppg-dashboard', 'manage-jadwal-produksi']);

        // Staf Gizi
        $roleModels['Staf Gizi']->syncPermissions(['perform-verifikasi-pangan', 'view-sppg-dashboard']);

        // Staf Akuntan
        $roleModels['Staf Akuntan']->syncPermissions(['view-sppg-reports', 'manage-sppg-finance', 'view-sppg-dashboard']);

        // Staf Akuntan Kornas (NEW)
        $roleModels['Staf Akuntan Kornas']->syncPermissions([
            'view-national-dashboard',
            'view-national-reports',
            'manage-sppg-finance', // Reusing this for general finance management
            'confirm-kornas-deposit',
        ]);

        // Staf Pengantaran
        $roleModels['Staf Pengantaran']->syncPermissions(['confirm-distribusi', 'view-sppg-dashboard']);


        // 5. Buat Users Bawaan
        // User Level Nasional (tidak terikat SPPG)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@mbm.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('p4$$w0rd')]
        );
        $superAdmin->assignRole($roleModels['Superadmin']);

        $direktur = User::firstOrCreate(
            ['email' => 'direktur.kornas@mbm.com'],
            ['name' => 'Direktur Kornas MBM', 'password' => Hash::make('p4$$w0rd')]
        );
        $direktur->assignRole($roleModels['Direktur Kornas']);

        $stafKornas = User::firstOrCreate(
            ['email' => 'staf.kornas@mbm.com'],
            ['name' => 'Staf Kornas MBM', 'password' => Hash::make('p4$$w0rd')]
        );
        $stafKornas->assignRole($roleModels['Staf Kornas']);

        $stafAkuntanKornas = User::firstOrCreate(
            ['email' => 'akuntan.kornas@mbm.com'],
            ['name' => 'Staf Akuntan Kornas MBM', 'password' => Hash::make('p4$$w0rd')]
        );
        $stafAkuntanKornas->assignRole($roleModels['Staf Akuntan Kornas']);


        // User Level SPPG (terikat pada SPPG pertama yang ada di database)
        $firstSppg = Sppg::first();
        if ($firstSppg) {
            $sppgUsers = [
                ['email' => 'kepala.sppg.pcmbaki@mbm.com', 'name' => 'Kepala SPPG PCM BAKI', 'role' => 'Kepala SPPG'],
                ['email' => 'admin.sppg.pcmbaki@mbm.com', 'name' => 'Admin SPPG PCM BAKI', 'role' => 'Staf Administrator SPPG'],
                ['email' => 'gizi.sppg.pcmbaki@mbm.com', 'name' => 'Gizi SPPG PCM BAKI', 'role' => 'Staf Gizi'],
                ['email' => 'kurir.sppg.pcmbaki@mbm.com', 'name' => 'Kurir SPPG PCM BAKI', 'role' => 'Staf Pengantaran'],
                ['email' => 'akuntan.sppg.pcmbaki@mbm.com', 'name' => 'Akuntan SPPG PCM BAKI', 'role' => 'Staf Akuntan'],
                ['email' => 'pj.sppg.pcmbaki@mbm.com', 'name' => 'PJ Pelaksana SPPG PCM BAKI', 'role' => 'PJ Pelaksana'],
                ['email' => 'kuasa.sppg.pcmbaki@mbm.com', 'name' => 'Penerima Kuasa SPPG PCM BAKI', 'role' => 'Penerima Kuasa'],
            ];

            foreach ($sppgUsers as $userData) {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    ['name' => $userData['name'], 'password' => Hash::make('p4$$w0rd')]
                );
                $user->assignRole($userData['role'], $firstSppg->id);
            }
        }
    }
}