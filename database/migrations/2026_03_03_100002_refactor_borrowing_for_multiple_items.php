<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create borrow_items table
        Schema::create('borrow_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_transaction_id')->constrained('borrow_transactions')->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained('inventories');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('quantity_returned')->default(0);
            $table->timestamps();
        });

        // 2. Migration data from existing borrow_transactions to borrow_items
        $borrows = DB::table('borrow_transactions')->get();
        foreach ($borrows as $borrow) {
            if ($borrow->inventory_id && $borrow->quantity) {
                DB::table('borrow_items')->insert([
                    'borrow_transaction_id' => $borrow->id,
                    'inventory_id'          => $borrow->inventory_id,
                    'quantity'              => $borrow->quantity,
                    'created_at'            => $borrow->created_at,
                    'updated_at'            => $borrow->updated_at,
                ]);
            }
        }

        // 3. Update return_transactions to link to individual items
        Schema::table('return_transactions', function (Blueprint $table) {
            $table->foreignId('borrow_item_id')->nullable()->after('borrow_transaction_id')->constrained('borrow_items');
        });

        // Try to link existing return transactions to the newly created borrow items
        $returns = DB::table('return_transactions')->get();
        foreach ($returns as $ret) {
            $item = DB::table('borrow_items')->where('borrow_transaction_id', $ret->borrow_transaction_id)->first();
            if ($item) {
                DB::table('return_transactions')->where('id', $ret->id)->update(['borrow_item_id' => $item->id]);
            }
        }

        // 4. Remove inventory_id and quantity from borrow_transactions
        Schema::table('borrow_transactions', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropColumn(['inventory_id', 'quantity']);
        });
    }

    public function down(): void
    {
        Schema::table('borrow_transactions', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable()->constrained('inventories');
            $table->unsignedInteger('quantity')->nullable();
        });

        // Re-migration data (approximate)
        $items = DB::table('borrow_items')->get();
        foreach ($items as $item) {
            DB::table('borrow_transactions')->where('id', $item->borrow_transaction_id)->update([
                'inventory_id' => $item->inventory_id,
                'quantity'     => $item->quantity
            ]);
        }

        Schema::table('return_transactions', function (Blueprint $table) {
            $table->dropForeign(['borrow_item_id']);
            $table->dropColumn('borrow_item_id');
        });

        Schema::dropIfExists('borrow_items');
    }
};
