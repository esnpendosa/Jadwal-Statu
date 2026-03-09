<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('return_transactions', function (Blueprint $table) {
            $table->unsignedInteger('quantity_poor')->default(0)->after('quantity_good');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_transactions', function (Blueprint $table) {
            $table->dropColumn('quantity_poor');
        });
    }
};
