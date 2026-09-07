<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->foreignId('stock_cut_id')
                  ->nullable()
                  ->after('stock_in_request_id')
                  ->constrained('stock_cuts')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->dropForeign(['stock_cut_id']);
            $table->dropColumn('stock_cut_id');
        });
    }
};
