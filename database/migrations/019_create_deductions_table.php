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
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['main', 'specific', 'console']);
            $table->decimal('customer_deduction', 10, 2);
            $table->decimal('my_deduction', 10, 2);
            $table->decimal('tree_deduction', 10, 2)->nullable();
            $table->decimal('floor_deduction', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
