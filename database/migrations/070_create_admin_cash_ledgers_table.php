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
        if (!Schema::hasTable('admin_cash_ledgers')) {
            Schema::create('admin_cash_ledgers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type'); // 'cash-in', 'supplier_payment', 'company_cost', 'cash_transfer'
                $table->decimal('amount', 10, 2);
                $table->decimal('cash_before', 10, 2)->default(0);
                $table->decimal('cash_after', 10, 2)->default(0);
                $table->string('payment_method')->nullable(); // 'cash', 'bank', 'cheque', 'bkash'
                $table->string('status')->default('complete');
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_cash_ledgers');
    }
};

