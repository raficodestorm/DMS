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
        // Add tree_deduction to stock_in_items
        Schema::table('stock_in_items', function (Blueprint $table) {
            $table->decimal('tree_deduction', 10, 2)->default(0)->after('cost_price');
        });

        // Add purchase_price to products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->default(0)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_in_items', function (Blueprint $table) {
            $table->dropColumn('tree_deduction');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
        });
    }
};
