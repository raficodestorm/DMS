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
        Schema::create('offer_slide', function (Blueprint $table) {
            $table->foreignId('slide_id')->constrained('slides')->cascadeOnDelete();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->primary(['slide_id', 'offer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_slide');
    }
};
