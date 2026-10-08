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
        Schema::create('cash_transfers', function (Blueprint $table) {
    $table->id();

    // Transfer From Branch
    $table->foreignId('branch_id')
        ->constrained('branches')
        ->cascadeOnDelete();

    $table->decimal('amount', 10, 2)->default(0);
    $table->decimal('cash_before_transfer', 10, 2)->default(0);
    $table->decimal('cash_after_transfer', 10, 2)->default(0);
    // Who Requested Transfer
    $table->foreignId('requested_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    // Status
    $table->enum('status', [
        'pending',
        'approved',
        'rejected',
    ])->default('pending');

    // Optional Notes
    $table->text('note')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transfers');
    }
};
