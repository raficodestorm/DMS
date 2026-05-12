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
        Schema::create('company_costs', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->date('cost_date');
            $table->enum('category', [
                'office', 
                'transport', 
                'salary', 
                'maintenance', 
                'product', 
                'utility', 
                'marketing', 
                'miscellaneous'
            ]);
            $table->string('description');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index('cost_date');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_costs');
    }
};
