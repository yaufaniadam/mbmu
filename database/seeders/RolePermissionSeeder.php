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
            'Delete:ProductionSchedule',

            // Complaint
            'ViewAny:Complaint',
            'View:Complaint',
            'Create:Complaint',
            'Update:Complaint',
            'Delete:Complaint'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Buat Roles (Peran Pengguna)
        $roles = [
            'Superadmin',
            'Ketua Kornas',
            'Sekretaris Kornas',
            'Bendahara Kornas',
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
        // Ketua Kornas
        $roleModels['Ketua Kornas']->syncPermissions([
            'view-national-dashboard',
            'view-national-reports',
            'ViewAny:Complaint',
            'View:Complaint',
            'Update:Complaint',
        ]);

        // Sekretaris Kornas - Same permissions as Direktur
        $roleModels['Sekretaris Kornas']->syncPermissions([
            'view-national-dashboard',
            'view-national-reports',
            'ViewAny:Complaint',
            'View:Complaint',
            'Update:Complaint',
        ]);

        // Bendahara Kornas - Same permissions as Direktur
        $roleModels['Bendahara Kornas']->syncPermissions([
            'view-national-dashboard',
            'view-national-reports',
            'ViewAny:Complaint',
            'View:Complaint',
            'Update:Complaint',
        ]);

        // Staf Kornas
        $roleModels['Staf Kornas']->syncPermissions([
            'view-national-dashboard',
            'view-national-reports',
            'manage-sppg',
            'manage-all-users',
            'manage-lembaga-pengusul',
            'confirm-kornas-deposit',
            'ViewAny:Complaint',
            'View:Complaint',
            'Update:Complaint',
        ]);

        // Pimpinan Lembaga Pengusul
        $roleModels['Pimpinan Lembaga Pengusul']->syncPermissions([
            'view-sppg-dashboard',
            'view-sppg-reports',
            'ViewAny:Complaint',
            'View:Complaint',
            'Create:Complaint',
            'Update:Complaint',
        ]);

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
            'Delete:ProductionSchedule',
            // Complaint
            'ViewAny:Complaint',
            'View:Complaint',
            'Create:Complaint',
            'Update:Complaint',
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
            'Delete:ProductionSchedule',
            // Complaint
            'ViewAny:Complaint',
            'View:Complaint',
            'Create:Complaint',
            'Update:Complaint',
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
            'Update:ProductionSchedule',
            // Complaint
            'ViewAny:Complaint',
            'View:Complaint',
            'Create:Complaint',
            'Update:Complaint',
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
            'ViewAny:Complaint',
            'View:Complaint',
            'Update:Complaint',
        ]);

        // Staf Pengantaran
        $roleModels['Staf Pengantaran']->syncPermissions(['confirm-distribusi', 'view-sppg-dashboard']);


        // 5. Buat Users Bawaan
        // User Level Nasional (tidak terikat SPPG)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@mbmu.id'],
            ['name' => 'Super Admin', 'password' => Hash::make('mBm@2025')]
        );
        $superAdmin->assignRole($roleModels['Superadmin']);

        $direktur = User::firstOrCreate(
            ['email' => 'ketua.kornas@mbmu.id'],
            ['name' => 'Ketua Kornas MBM', 'password' => Hash::make('ketuaKornas')]
        );
        $direktur->assignRole($roleModels['Ketua Kornas']);

        $stafKornas = User::firstOrCreate(
            ['email' => 'staf.kornas@mbmu.id'],
            ['name' => 'Staf Kornas MBM', 'password' => Hash::make('stafKornas')]
        );
        $stafKornas->assignRole($roleModels['Staf Kornas']);

        $stafAkuntanKornas = User::firstOrCreate(
            ['email' => 'akuntan.kornas@mbmu.id'],
            ['name' => 'Staf Akuntan Kornas MBM', 'password' => Hash::make('akuntanKornas')]
        );
        
        // Create Sekretaris and Bendahara default users
        $sekretaris = User::firstOrCreate(
            ['email' => 'sekretaris.kornas@mbmu.id'],
            ['name' => 'Sekretaris Kornas MBM', 'password' => Hash::make('sekretarisKornas')]
        );
        $sekretaris->assignRole($roleModels['Sekretaris Kornas']);

        $bendahara = User::firstOrCreate(
            ['email' => 'bendahara.kornas@mbmu.id'],
            ['name' => 'Bendahara Kornas MBM', 'password' => Hash::make('bendaharaKornas')]
        );
        $bendahara->assignRole($roleModels['Bendahara Kornas']);
    }
}