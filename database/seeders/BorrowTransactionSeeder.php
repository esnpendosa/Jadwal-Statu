<?php

namespace Database\Seeders;

use App\Models\BorrowTransaction;
use App\Models\BorrowItem;
use App\Models\Inventory;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BorrowTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $inventory = Inventory::where('stock_available', '>', 0)->get();
        $users = User::role('PIC')->get();

        if ($projects->isEmpty() || $inventory->isEmpty() || $users->isEmpty()) {
            return;
        }

        // Active Borrow
        for ($i = 0; $i < 5; $i++) {
            $borrow = BorrowTransaction::create([
                'code' => 'BRW-' . strtoupper(Str::random(8)),
                'project_id' => $projects->random()->id,
                'requested_by' => $users->random()->id,
                'borrow_date' => Carbon::now()->subDays(rand(1, 10)),
                'expected_return_date' => Carbon::now()->addDays(rand(5, 15)),
                'status' => 'borrowed',
                'purpose' => 'Generic equipment borrow for site tasks.',
            ]);

            $item = $inventory->random();
            BorrowItem::create([
                'borrow_transaction_id' => $borrow->id,
                'inventory_id'          => $item->id,
                'quantity'              => rand(1, 2),
            ]);
        }

        // Overdue Borrow
        for ($i = 0; $i < 2; $i++) {
            $borrow = BorrowTransaction::create([
                'code' => 'BRW-' . strtoupper(Str::random(8)),
                'project_id' => $projects->random()->id,
                'requested_by' => $users->random()->id,
                'borrow_date' => Carbon::now()->subMonths(1),
                'expected_return_date' => Carbon::now()->subDays(5),
                'status' => 'borrowed',
                'purpose' => 'Overdue equipment - testing alert system.',
            ]);

            $item = $inventory->random();
            BorrowItem::create([
                'borrow_transaction_id' => $borrow->id,
                'inventory_id'          => $item->id,
                'quantity'              => 1,
            ]);
        }

        // Returned (Completed)
        for ($i = 0; $i < 3; $i++) {
            $borrow = BorrowTransaction::create([
                'code' => 'BRW-' . strtoupper(Str::random(8)),
                'project_id' => $projects->random()->id,
                'requested_by' => $users->random()->id,
                'borrow_date' => Carbon::now()->subMonths(2),
                'expected_return_date' => Carbon::now()->subMonths(1),
                'actual_return_date' => Carbon::now()->subMonths(1)->addDays(2),
                'status' => 'completed',
            ]);

            $item = $inventory->random();
            BorrowItem::create([
                'borrow_transaction_id' => $borrow->id,
                'inventory_id'          => $item->id,
                'quantity'              => 1,
                'quantity_returned'     => 1,
            ]);
        }
    }
}
