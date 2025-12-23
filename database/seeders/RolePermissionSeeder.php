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
            'manage-sppg-profile',
            'confirm-kornas-deposit', // Khusus Staf Kornas

            // Policy-based permissions for Production Schedule
            'ViewAny:ProductionSchedule',
            'View:ProductionSchedule',
            'Create:ProductionSchedule',
            'Update:ProductionSchedule',
            'Delete:ProductionSchedule'
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
            'manage-sppg-finance',
            'manage-sppg-profile',
            // Production Schedule
            'ViewAny:ProductionSchedule',
            'View:ProductionSchedule',
            'Create:ProductionSchedule',
            'Update:ProductionSchedule',
            'Delete:ProductionSchedule'
        ]);

        // PJ Pelaksana
        $roleModels['PJ Pelaksana']->syncPermissions([
            'view-sppg-dashboard',
            'view-sppg-reports',
            // Production Schedule
            'ViewAny:ProductionSchedule',
            'View:ProductionSchedule',
            'Create:ProductionSchedule',
            'Update:ProductionSchedule',
            'Delete:ProductionSchedule'
        ]);

        // Penerima Kuasa
        $roleModels['Penerima Kuasa']->syncPermissions(['view-sppg-dashboard', 'view-sppg-reports']);

        // Staf Administrator SPPG
        $roleModels['Staf Administrator SPPG']->syncPermissions([
            'view-sppg-dashboard', 
            'manage-jadwal-produksi',
            'manage-sppg-profile',
            // Production Schedule
            'ViewAny:ProductionSchedule',
            'View:ProductionSchedule',
            'Create:ProductionSchedule',
            'Update:ProductionSchedule'
        ]);

        // Staf Gizi
        $roleModels['Staf Gizi']->syncPermissions([
            'perform-verifikasi-pangan',
            'view-sppg-dashboard',
            // Production Schedule (view only)
            'ViewAny:ProductionSchedule',
            'View:ProductionSchedule',
        ]);

        // Staf Akuntan
        $roleModels['Staf Akuntan']->syncPermissions([
            'view-sppg-reports',
            'manage-sppg-finance',
            'view-sppg-dashboard',
            // Production Schedule (view only)
            'ViewAny:ProductionSchedule',
            'View:ProductionSchedule',
        ]);

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
    }
}