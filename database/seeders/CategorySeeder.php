<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik',        'icon' => 'cpu-chip',     'color' => '#6366f1'],
            ['name' => 'Alat Ukur',          'icon' => 'scale',        'color' => '#f59e0b'],
            ['name' => 'Peralatan Tangan',   'icon' => 'wrench',       'color' => '#10b981'],
            ['name' => 'Peralatan Berat',    'icon' => 'cog',          'color' => '#ef4444'],
            ['name' => 'Keselamatan',        'icon' => 'shield-check', 'color' => '#3b82f6'],
            ['name' => 'Kendaraan',          'icon' => 'truck',        'color' => '#8b5cf6'],
            ['name' => 'Komputer & IT',      'icon' => 'computer',     'color' => '#06b6d4'],
            ['name' => 'Furnitur',           'icon' => 'home',         'color' => '#d97706'],
            ['name' => 'Komunikasi',         'icon' => 'phone',        'color' => '#0ea5e9'],
            ['name' => 'Lainnya',            'icon' => 'ellipsis',     'color' => '#6b7280'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, ['slug' => Str::slug($cat['name']), 'is_active' => true])
            );
        }
    }
}
