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
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('stock_in_request_id')->nullable()->constrained('stock_in_requests')->nullOnDelete();
            
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('type');
            // 'pay', 'buy', 'return', adjustment 'opening_balance'
            $table->decimal('amount', 10, 2);
            //--- new-------------------------------
            $table->decimal('due_before_transaction', 10, 2)->nullable();
            $table->decimal('due_after_transaction', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            // cash, bank, cheque --------------------------------
            
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_transactions');
    }
};
