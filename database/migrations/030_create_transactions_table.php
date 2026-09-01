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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('sr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('type');
            // 'pay', 'buy', 'return','opening_balance'
            $table->decimal('amount', 10, 2);
            //--- new-------------------------------
            $table->decimal('due_before_transaction', 10, 2)->nullable();
            $table->decimal('due_after_transaction', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            // cash, bank, cheque --------------------------------
            $table->string('status');
            // 'pending', 'complete'
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
