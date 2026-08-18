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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('sr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_id')->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending_sr', 'pending_manager', 'approved', 'rejected', 'complete', 'delivered']);
            $table->decimal('special_discount', 10, 2)->default(0)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('net_total', 10, 2);
            $table->decimal('applied_deduction_percent', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('order_type')->default('field_order');
            // 'retail',
            // 'field_order',
            // 'online',
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
