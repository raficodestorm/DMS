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
        Schema::create('stock_transfers', function (Blueprint $table) {
    $table->id();

    // Transfer From Branch
    $table->foreignId('from_branch_id')
        ->constrained('branches')
        ->cascadeOnDelete();

    // Transfer To Branch
    $table->foreignId('to_branch_id')
        ->constrained('branches')
        ->cascadeOnDelete();

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
        'completed'
    ])->default('pending');

    // Optional Notes
    $table->text('note')->nullable();

    // Approval Time
    $table->timestamp('approved_at')->nullable();

    // Completion Time
    $table->timestamp('completed_at')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
