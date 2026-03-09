<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@statusscheduler.com'],
            [
                'name'     => 'Admin',
                'password' => bcrypt('admin123'),
            ]
        );

        // Seed default settings
        $defaults = [
            'late_api_key'    => config('services.late.api_key', ''),
            'late_profile_id' => config('services.late.profile_id', ''),
            'fonnte_token'    => config('services.fonnte.token', ''),
            'whatsapp_target' => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('✓ Admin user created: admin@statusscheduler.com / admin123');
        $this->command->info('✓ Default settings seeded.');
    }
}
