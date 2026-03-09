<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            InventorySeeder::class,
            ProjectSeeder::class,
            BorrowTransactionSeeder::class,
            RiskRuleSeeder::class,
            AiSuggestionRuleSeeder::class,
            SystemSettingSeeder::class,
            EmailTemplateSeeder::class,
            DummyAiDataSeeder::class,
        ]);
    }
}
