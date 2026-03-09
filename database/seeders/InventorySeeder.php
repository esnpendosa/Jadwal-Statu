<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        
        $items = [
            [
                'name' => 'MacBook Pro M3 14"',
                'code' => 'LAP-MBP-001',
                'description' => 'High performance laptop for development and design.',
                'stock_total' => 15,
                'stock_available' => 10,
                'condition' => 'good',
            ],
            [
                'name' => 'Dell XPS 15',
                'code' => 'LAP-DEL-002',
                'description' => 'Reliable workstation for project managers.',
                'stock_total' => 10,
                'stock_available' => 8,
                'condition' => 'good',
            ],
            [
                'name' => 'Cisco Router ISR 4331',
                'code' => 'NET-CIS-001',
                'description' => 'Core networking equipment for site infrastructure.',
                'stock_total' => 5,
                'stock_available' => 2,
                'condition' => 'good',
            ],
            [
                'name' => 'Samsung 27" 4K Monitor',
                'code' => 'MON-SAM-001',
                'description' => 'External display for site office setup.',
                'stock_total' => 20,
                'stock_available' => 15,
                'condition' => 'good',
            ],
            [
                'name' => 'Drone DJI Mavic 3',
                'code' => 'TLS-DRN-001',
                'description' => 'Aerial surveillance and site mapping tool.',
                'stock_total' => 3,
                'stock_available' => 1,
                'condition' => 'good',
            ],
            [
                'name' => 'Industrial Generator 500kVA',
                'code' => 'PWR-GEN-001',
                'description' => 'Heavy duty power supply for remote projects.',
                'stock_total' => 2,
                'stock_available' => 2,
                'condition' => 'maintenance',
            ],
        ];

        foreach ($items as $item) {
            $category = $categories->random();
            $admin = \App\Models\User::role('Admin')->first() ?? \App\Models\User::first();
            Inventory::create(array_merge($item, [
                'category_id' => $category->id,
                'created_by' => $admin->id,
            ]));
        }
    }
}
