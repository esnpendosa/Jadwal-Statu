<?php

namespace Database\Seeders;

use App\Models\RiskRule;
use Illuminate\Database\Seeder;

class RiskRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'code'        => 'late_return',
                'name'        => 'Keterlambatan Pengembalian',
                'description' => 'Poin diberikan ketika barang dikembalikan melewati batas waktu.',
                'points'      => (int) env('RISK_LATE_POINTS', 2),
                'category'    => 'behavior',
                'is_active'   => true,
            ],
            [
                'code'        => 'quantity_mismatch',
                'name'        => 'Selisih Jumlah (Kehilangan)',
                'description' => 'Poin diberikan ketika jumlah barang yang dikembalikan kurang dari yang dipinjam.',
                'points'      => (int) env('RISK_MISMATCH_POINTS', 3),
                'category'    => 'loss',
                'is_active'   => true,
            ],
            [
                'code'        => 'damage',
                'name'        => 'Kerusakan Barang',
                'description' => 'Poin diberikan ketika barang dikembalikan dalam kondisi rusak.',
                'points'      => (int) env('RISK_DAMAGE_POINTS', 5),
                'category'    => 'damage',
                'is_active'   => true,
            ],
        ];

        foreach ($rules as $rule) {
            RiskRule::firstOrCreate(['code' => $rule['code']], $rule);
        }
    }
}
