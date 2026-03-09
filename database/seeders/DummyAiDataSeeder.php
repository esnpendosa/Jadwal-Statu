<?php

namespace Database\Seeders;

use App\Models\AiSuggestion;
use App\Models\AiSuggestionRule;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyAiDataSeeder extends Seeder
{
    public function run(): void
    {
        $pic = User::role('PIC')->first();
        if (!$pic) return;

        $rules = AiSuggestionRule::all();
        $inventory = Inventory::first();

        // 1. PIC related suggestion
        AiSuggestion::create([
            'ai_suggestion_rule_id' => $rules->where('trigger_type', 'late_return_frequency')->first()->id,
            'target_type'           => 'user',
            'target_id'             => $pic->id,
            'target_label'          => $pic->name,
            'suggestion_text'       => 'PIC ini memiliki tren keterlambatan 30% pada proyek terakhir. Disarankan monitoring ketat pada deadline pengembalian.',
            'severity'              => 'warning',
            'generated_at'          => now(),
        ]);

        // 2. Damage related suggestion
        AiSuggestion::create([
            'ai_suggestion_rule_id' => $rules->where('trigger_type', 'damage_frequency')->first()->id,
            'target_type'           => 'user',
            'target_id'             => $pic->id,
            'target_label'          => $pic->name,
            'suggestion_text'       => 'Terdeteksi kerusakan berulang pada alat berat di lokasi proyek aktif. Rekomendasi: Lakukan inspeksi alat mingguan.',
            'severity'              => 'critical',
            'generated_at'          => now()->subDay(),
        ]);

        // 3. Inventory related suggestion
        if ($inventory) {
            AiSuggestion::create([
                'ai_suggestion_rule_id' => $rules->first()->id,
                'target_type'           => 'inventory',
                'target_id'             => $inventory->id,
                'target_label'          => $inventory->name,
                'suggestion_text'       => 'Stok alat ini kritis sedang digunakan di 3 proyek sekaligus. Segera lakukan pengadaan atau penarikan stok cadangan.',
                'severity'              => 'critical',
                'generated_at'          => now()->subHours(2),
            ]);
        }
    }
}
