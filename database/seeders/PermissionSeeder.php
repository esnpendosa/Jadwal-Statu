<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Inventory
            'view inventory', 'create inventory', 'edit inventory', 'delete inventory', 'manage categories',
            // Projects
            'view project', 'create project', 'edit project', 'delete project',
            // Borrow
            'view borrow', 'create borrow', 'approve borrow',
            // Return
            'view return', 'create return', 'verify return',
            // Reports
            'view reports',
            // Admin
            'access admin panel', 'manage users', 'manage roles', 'manage settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
    }
}
