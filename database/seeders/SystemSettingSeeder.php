<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['group' => 'general', 'key' => 'app_name',        'value' => 'Smart Inventory',           'type' => 'string',  'label' => 'Application Name'],
            ['group' => 'general', 'key' => 'app_timezone',    'value' => 'Asia/Jakarta',               'type' => 'string',  'label' => 'Default Timezone'],
            ['group' => 'general', 'key' => 'default_language','value' => 'id',                         'type' => 'string',  'label' => 'Default Language'],
            // Reminders
            ['group' => 'reminder', 'key' => 'reminder_enabled','value' => '1',                         'type' => 'boolean', 'label' => 'Enable Email Reminders'],
            ['group' => 'reminder', 'key' => 'reminder_days_before','value' => '1',                     'type' => 'integer', 'label' => 'Remind N Days Before Deadline'],
            // Risk
            ['group' => 'risk',    'key' => 'risk_high_threshold',  'value' => '10',                    'type' => 'integer', 'label' => 'High Risk Threshold (points)'],
            ['group' => 'risk',    'key' => 'risk_critical_threshold','value' => '20',                   'type' => 'integer', 'label' => 'Critical Risk Threshold (points)'],
            // Inventory
            ['group' => 'inventory','key' => 'low_stock_notify_admin','value' => '1',                   'type' => 'boolean', 'label' => 'Notify Admin on Low Stock'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
