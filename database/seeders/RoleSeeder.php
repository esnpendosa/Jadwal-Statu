<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya 2 role: Admin (akses penuh) dan PIC (akses terbatas)
        $roles = [
            'Admin' => Permission::all()->pluck('name')->toArray(),

            'PIC' => [
                'view inventory',
                'view project',
                'view borrow', 'create borrow',
                'view return', 'create return',
            ],
        ];

        foreach ($roles as $role => $perms) {
            $r = Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
            $r->syncPermissions($perms);
        }
    }
}
