<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'               => 'Administrator',
                'email'              => 'aadscreet@gmail.com',
                'password'           => Hash::make('password'),
                'preferred_language' => 'id',
                'is_active'          => true,
                'role'               => 'Admin',
            ],
            [
                'name'               => 'PIC Project',
                'email'              => 'kangdigitall@gmail.com',
                'password'           => Hash::make('password'),
                'preferred_language' => 'id',
                'is_active'          => true,
                'role'               => 'PIC',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->syncRoles([$role]);
        }
    }
}
