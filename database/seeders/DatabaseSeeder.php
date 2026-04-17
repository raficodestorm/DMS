<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(BranchSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(ProductSeeder::class);

        // Supplier::factory()->count(10)->create();

        // \App\Models\Order::factory()->count(10)->create([
        // 'customer_id' => \App\Models\Customer::inRandomOrder()->first()->id, 
        // ]);
    }
}
