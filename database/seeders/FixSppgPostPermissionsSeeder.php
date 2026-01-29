<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixSppgPostPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Ensure permissions exist
        $permissions = [
            'view_any_post',
            'view_post',
            'create_post',
            'update_post',
            'delete_post',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign to Kepala SPPG role
        $role = Role::firstOrCreate(['name' => 'Kepala SPPG', 'guard_name' => 'web']);
        
        $role->givePermissionTo([
            'view_any_post',
            'view_post',
            'create_post',
            'update_post',
        ]);
        
        $this->command->info('Permissions assigned to Kepala SPPG.');
    }
}
