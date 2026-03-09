<?php

namespace Database\Seeders;

use App\Models\AiSuggestionRule;
use Illuminate\Database\Seeder;

class AiSuggestionRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'trigger_type' => 'late_return_frequency',
                'threshold'    => 3,
                'period_days'  => 30,
                'target'       => 'pic',
                'severity'     => 'warning',
                'sort_order'   => 1,
                'is_active'    => true,
                'suggestion'   => [
                    'id' => 'PIC ini sering mengalami keterlambatan pengembalian. Disarankan evaluasi jadwal proyek atau tambahkan buffer waktu yang lebih realistis.',
                    'en' => 'This PIC frequently has late returns. Consider evaluating the project schedule or adding more realistic time buffers.',
                    'zh' => '此PIC经常延迟归还。建议评估项目时间表或增加更实际的缓冲时间。',
                ],
            ],
            [
                'trigger_type' => 'damage_frequency',
                'threshold'    => 2,
                'period_days'  => 30,
                'target'       => 'pic',
                'severity'     => 'critical',
                'sort_order'   => 2,
                'is_active'    => true,
                'suggestion'   => [
                    'id' => 'Tingkat kerusakan barang tinggi. Pertimbangkan audit internal, evaluasi cara penggunaan alat, dan evaluasi kinerja PIC bersangkutan.',
                    'en' => 'High damage rate detected. Consider an internal audit, review equipment usage procedures, and evaluate the PIC performance.',
                    'zh' => '检测到高损坏率。考虑进行内部审计，审查设备使用程序，并评估PIC绩效。',
                ],
            ],
            [
                'trigger_type' => 'mismatch_frequency',
                'threshold'    => 2,
                'period_days'  => 30,
                'target'       => 'pic',
                'severity'     => 'warning',
                'sort_order'   => 3,
                'is_active'    => true,
                'suggestion'   => [
                    'id' => 'Barang kecil sering hilang. Gunakan checklist harian sebelum meninggalkan lokasi proyek dan tingkatkan pengawasan pada barang-barang kecil.',
                    'en' => 'Small items are frequently lost. Implement a daily checklist before leaving the project site and increase oversight of small items.',
                    'zh' => '小物品经常丢失。在离开项目现场之前实施每日检查清单，并加强对小物品的监督。',
                ],
            ],
            [
                'trigger_type' => 'high_risk_score',
                'threshold'    => 15,
                'period_days'  => 30,
                'target'       => 'pic',
                'severity'     => 'critical',
                'sort_order'   => 4,
                'is_active'    => true,
                'suggestion'   => [
                    'id' => 'Skor risiko PIC ini sangat tinggi. Diperlukan tindakan segera: lakukan evaluasi kinerja, training penggunaan alat, dan pertimbangkan pembatasan peminjaman.',
                    'en' => 'This PIC has a very high risk score. Immediate action needed: performance review, equipment usage training, and consider restricting borrowing privileges.',
                    'zh' => '此PIC的风险评分非常高。需要立即采取行动：绩效审查、设备使用培训，并考虑限制借用权限。',
                ],
            ],
        ];

        foreach ($rules as $rule) {
            AiSuggestionRule::firstOrCreate(
                ['trigger_type' => $rule['trigger_type'], 'target' => $rule['target']],
                $rule
            );
        }
    }
}
