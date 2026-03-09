<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua user PIC aktif
        $pics = User::role('PIC')->get();

        if ($pics->isEmpty()) {
            return;
        }

        $projects = [
            [
                'name'        => 'Gedung Smart Hub Jakarta',
                'description' => 'Construction and IT infrastructure for Jakarta Smart Hub.',
                'location'    => 'Jakarta, Indonesia',
                'status'      => 'active',
                'start_date'  => Carbon::now()->subMonths(2),
                'end_date'    => Carbon::now()->addMonths(6),
                'manager_name'=> 'Budi Santoso', // input manual
            ],
            [
                'name'        => 'Modern Warehouse Bali',
                'description' => 'Automated warehouse system deployment in Bali logistics center.',
                'location'    => 'Bali, Indonesia',
                'status'      => 'draft',
                'start_date'  => Carbon::now()->addMonth(),
                'end_date'    => Carbon::now()->addMonths(12),
                'manager_name'=> 'Siti Rahayu',
            ],
            [
                'name'        => 'Data Center Surabaya',
                'description' => 'Upgrading cooling systems and server racks for Surabaya DC.',
                'location'    => 'Surabaya, Indonesia',
                'status'      => 'active',
                'start_date'  => Carbon::now()->subMonth(),
                'end_date'    => Carbon::now()->addMonths(8),
                'manager_name'=> 'Ahmad Fauzi',
            ],
            [
                'name'        => 'Eco-Park Solar Grid',
                'description' => 'Installing solar panels and battery storage for Eco-Park project.',
                'location'    => 'Bandung, Indonesia',
                'status'      => 'completed',
                'start_date'  => Carbon::now()->subMonths(12),
                'end_date'    => Carbon::now()->subMonths(2),
                'manager_name'=> 'Diana Putri',
            ],
        ];

        $admin = User::role('Admin')->first() ?? User::first();

        foreach ($projects as $proj) {
            Project::create(array_merge($proj, [
                'code'       => 'PRJ-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'pic_id'     => $pics->random()->id,
                'created_by' => $admin->id,
                'risk_score' => rand(0, 30),
            ]));
        }
    }
}
