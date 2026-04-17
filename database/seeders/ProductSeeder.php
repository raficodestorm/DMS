<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $products = [
            //  -- 1
            [
                'name' => 'Fiona 1 gang switch',
                'sku' => 'FGS1',
                'category_id' => 5,
                'supplier_id' => 1,
                'price' => 100,
                'stock_alert' => 10,
                'description' => 'This is best switch',
            ],
            //  -- 2
            [
                'name' => 'Fiona 2 gang switch',
                'sku' => 'FGS2',
                'category_id' => 5,
                'supplier_id' => 1,
                'price' => 150,
                'stock_alert' => 10,
                'description' => 'This is best switch',
            ],
            //  -- 3
            [
                'name' => 'Fiona 3 gang switch',
                'sku' => 'FGS3',
                'category_id' => 5,
                'supplier_id' => 1,
                'price' => 180,
                'stock_alert' => 10,
                'description' => 'This is best switch',
            ],

        ];

        foreach ($products as $product) {

            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
