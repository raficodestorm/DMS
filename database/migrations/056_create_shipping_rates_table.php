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
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);

            $table->string('country', 100);

            $table->string('city', 100);

            $table->decimal('base_rate', 12, 2);

            $table->boolean('status')
                ->default(true)
                ->index();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Prevent duplicate shipping rates for the same location
            |--------------------------------------------------------------------------
            */
            $table->unique(
                ['country', 'city'],
                'shipping_rates_country_city_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
