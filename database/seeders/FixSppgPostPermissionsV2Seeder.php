<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixSppgPostPermissionsV2Seeder extends Seeder
{
    public function run()
    {
        // Permissions expected by PostPolicy.php
        $permissions = [
            'ViewAny:Post',
            'View:Post',
            'Create:Post',
            'Update:Post',
            'Delete:Post',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign to Kepala SPPG role
        $role = Role::firstOrCreate(['name' => 'Kepala SPPG', 'guard_name' => 'web']);
        
        $role->givePermissionTo([
            'ViewAny:Post',
            'View:Post',
            'Create:Post',
            'Update:Post',
        ]);
        
        $this->command->info('Correct Permissions (Colon Format) assigned to Kepala SPPG.');
    }
}
