<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code'    => 'reminder_h1',
                'subject' => [
                    'id' => '⏰ Pengingat: Pengembalian Barang Besok - {{borrow_code}}',
                    'en' => '⏰ Reminder: Item Return Due Tomorrow - {{borrow_code}}',
                    'zh' => '⏰ 提醒：明天归还物品 - {{borrow_code}}',
                ],
                'body'    => [
                    'id' => '<p>Halo <strong>{{user_name}}</strong>,</p><p>Ini adalah pengingat bahwa Anda harus mengembalikan <strong>{{quantity}} {{item_name}}</strong> untuk proyek <strong>{{project_name}}</strong> besok pada tanggal <strong>{{expected_return_date}}</strong>.</p><p>Kode Peminjaman: <code>{{borrow_code}}</code></p><p>Harap pastikan barang dikembalikan tepat waktu dan dalam kondisi baik.</p><p>Terima kasih,<br>Tim Smart Inventory</p>',
                    'en' => '<p>Hello <strong>{{user_name}}</strong>,</p><p>This is a reminder that you need to return <strong>{{quantity}} {{item_name}}</strong> for project <strong>{{project_name}}</strong> tomorrow on <strong>{{expected_return_date}}</strong>.</p><p>Borrow Code: <code>{{borrow_code}}</code></p><p>Please ensure the item is returned on time and in good condition.</p><p>Thank you,<br>Smart Inventory Team</p>',
                    'zh' => '<p>您好 <strong>{{user_name}}</strong>，</p><p>提醒您明天需要归还 <strong>{{quantity}} {{item_name}}</strong>，用于项目 <strong>{{project_name}}</strong>，归还日期为 <strong>{{expected_return_date}}</strong>。</p><p>借用代码：<code>{{borrow_code}}</code></p><p>请确保物品准时归还且状态良好。</p><p>谢谢，<br>智能库存团队</p>',
                ],
                'is_active' => true,
            ],
            [
                'code'    => 'overdue',
                'subject' => [
                    'id' => '🚨 TERLAMBAT: Segera Kembalikan Barang - {{borrow_code}}',
                    'en' => '🚨 OVERDUE: Please Return Item Immediately - {{borrow_code}}',
                    'zh' => '🚨 逾期：请立即归还物品 - {{borrow_code}}',
                ],
                'body'    => [
                    'id' => '<p>Halo <strong>{{user_name}}</strong>,</p><p><strong style="color:red">PERINGATAN:</strong> Peminjaman Anda sudah melewati batas pengembalian!</p><p>Item: <strong>{{quantity}} {{item_name}}</strong><br>Proyek: <strong>{{project_name}}</strong><br>Batas Waktu: <strong>{{expected_return_date}}</strong></p><p>Segera kembalikan barang tersebut untuk menghindari penambahan poin risiko lebih lanjut.</p>',
                    'en' => '<p>Hello <strong>{{user_name}}</strong>,</p><p><strong style="color:red">WARNING:</strong> Your borrow is past the return deadline!</p><p>Item: <strong>{{quantity}} {{item_name}}</strong><br>Project: <strong>{{project_name}}</strong><br>Deadline: <strong>{{expected_return_date}}</strong></p><p>Please return the item immediately to avoid further risk score additions.</p>',
                    'zh' => '<p>您好 <strong>{{user_name}}</strong>，</p><p><strong style="color:red">警告：</strong>您的借用已超过归还截止日期！</p><p>物品：<strong>{{quantity}} {{item_name}}</strong><br>项目：<strong>{{project_name}}</strong><br>截止日期：<strong>{{expected_return_date}}</strong></p><p>请立即归还物品，以避免进一步增加风险分数。</p>',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::firstOrCreate(['code' => $template['code']], $template);
        }
    }
}
