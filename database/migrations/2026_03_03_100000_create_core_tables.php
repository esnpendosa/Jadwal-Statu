<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Login History
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // nullable: login gagal tidak ada user
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 20)->default('success'); // success, failed
            $table->timestamp('logged_in_at');
            $table->index(['user_id', 'logged_in_at']);
        });

        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('cube');
            $table->string('color', 20)->default('#6366f1');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // Inventories
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Unique barcode/item code');
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories');
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('unit', 50)->default('pcs')->comment('pcs, set, unit, box, etc');
            $table->unsignedInteger('stock_total')->default(0);
            $table->unsignedInteger('stock_available')->default(0);
            $table->unsignedInteger('stock_borrowed')->default(0)->comment('Currently borrowed');
            $table->unsignedInteger('stock_minimum')->default(1)->comment('Alert when below this');
            $table->string('condition', 30)->default('good')->comment('good, fair, poor, damaged');
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('location')->nullable()->comment('Storage location');
            $table->string('image')->nullable();
            $table->unsignedInteger('damaged_count')->default(0);
            $table->unsignedInteger('lost_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index('stock_available');
            $table->index('condition');
        });

        // Inventory stock history
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('action', 30)->comment('added, adjusted, borrowed, returned, damaged, lost');
            $table->integer('quantity_before');
            $table->integer('quantity_change');
            $table->integer('quantity_after');
            $table->string('notes')->nullable();
            $table->string('reference_type')->nullable()->comment('BorrowTransaction, ReturnTransaction, etc');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
            
            $table->index(['inventory_id', 'created_at']);
        });

        // Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->string('client_name')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 30)->default('active')->comment('draft, active, completed, cancelled');
            $table->foreignId('pic_id')->constrained('users')->comment('Person In Charge (dipilih dari daftar user PIC)');
            $table->string('manager_name')->nullable()->comment('Nama manajer — input manual teks bebas');
            $table->foreignId('created_by')->constrained('users');
            $table->decimal('budget', 15, 2)->nullable();
            $table->unsignedInteger('risk_score')->default(0);
            $table->string('risk_level', 20)->default('low')->comment('low, medium, high, critical');
            $table->string('google_calendar_event_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['status', 'end_date']);
            $table->index('pic_id');
            $table->index('risk_level');
        });

        // Borrow Transactions
        Schema::create('borrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('inventory_id')->constrained('inventories');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->unsignedInteger('quantity');
            $table->string('status', 30)->default('pending')->comment('pending, approved, borrowed, completed, rejected');
            $table->date('borrow_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->string('google_calendar_event_id')->nullable();
            $table->timestamps();
            
            $table->index(['project_id', 'status']);
            $table->index(['inventory_id', 'status']);
            $table->index(['expected_return_date', 'status']);
            $table->index('requested_by');
        });

        // Return Transactions
        Schema::create('return_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('borrow_transaction_id')->constrained('borrow_transactions');
            $table->foreignId('returned_by')->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->unsignedInteger('quantity_returned');
            $table->unsignedInteger('quantity_good')->default(0);
            $table->unsignedInteger('quantity_damaged')->default(0);
            $table->unsignedInteger('quantity_lost')->default(0);
            $table->string('condition_notes')->nullable();
            $table->boolean('is_late')->default(false);
            $table->integer('days_late')->default(0);
            $table->string('status', 20)->default('pending')->comment('pending, verified');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('borrow_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_transactions');
        Schema::dropIfExists('borrow_transactions');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('inventory_histories');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('login_histories');
    }
};
