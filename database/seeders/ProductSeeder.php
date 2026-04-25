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
            // -- 3
            [
                'name' => 'Fiona 3 gang switch',
                'sku' => 'FGS3',
                'category_id' => 5,
                'supplier_id' => 1,
                'price' => 180,
                'stock_alert' => 10,
                'description' => 'This is best switch',
            ],

            // -- 4
            [
                'name' => 'Fiona 2 gang switch',
                'sku' => 'FGS2',
                'category_id' => 5,
                'supplier_id' => 1,
                'price' => 140,
                'stock_alert' => 10,
                'description' => 'Premium quality switch',
            ],

            // -- 5
            [
                'name' => 'Fiona 1 gang switch',
                'sku' => 'FGS1',
                'category_id' => 5,
                'supplier_id' => 1,
                'price' => 95,
                'stock_alert' => 10,
                'description' => 'Durable wall switch',
            ],

            // -- 6
            [
                'name' => 'LED Bulb 12W',
                'sku' => 'LEDB12',
                'category_id' => 2,
                'supplier_id' => 1,
                'price' => 220,
                'stock_alert' => 15,
                'description' => 'Bright white LED bulb',
            ],

            // -- 7
            [
                'name' => 'LED Bulb 18W',
                'sku' => 'LEDB18',
                'category_id' => 2,
                'supplier_id' => 1,
                'price' => 320,
                'stock_alert' => 15,
                'description' => 'Energy saving LED bulb',
            ],

            // -- 8
            [
                'name' => 'LED Tube Light 20W',
                'sku' => 'LTL20',
                'category_id' => 2,
                'supplier_id' => 1,
                'price' => 450,
                'stock_alert' => 8,
                'description' => 'Long lasting tube light',
            ],

            // -- 9
            [
                'name' => 'Ceiling Fan 56 Inch',
                'sku' => 'CF56',
                'category_id' => 3,
                'supplier_id' => 1,
                'price' => 3200,
                'stock_alert' => 5,
                'description' => 'High speed ceiling fan',
            ],

            // -- 10
            [
                'name' => 'Table Fan 16 Inch',
                'sku' => 'TF16',
                'category_id' => 3,
                'supplier_id' => 1,
                'price' => 2800,
                'stock_alert' => 5,
                'description' => 'Portable table fan',
            ],

            // -- 11
            [
                'name' => 'Exhaust Fan 10 Inch',
                'sku' => 'EF10',
                'category_id' => 3,
                'supplier_id' => 1,
                'price' => 2500,
                'stock_alert' => 5,
                'description' => 'Kitchen exhaust fan',
            ],

            // -- 12
            [
                'name' => 'PVC Pipe 1 Inch',
                'sku' => 'PVC1',
                'category_id' => 4,
                'supplier_id' => 1,
                'price' => 380,
                'stock_alert' => 20,
                'description' => 'Strong PVC water pipe',
            ],

            // -- 13
            [
                'name' => 'PVC Pipe 2 Inch',
                'sku' => 'PVC2',
                'category_id' => 4,
                'supplier_id' => 1,
                'price' => 620,
                'stock_alert' => 20,
                'description' => 'Heavy duty PVC pipe',
            ],

            // -- 14
            [
                'name' => 'Socket 2 Pin',
                'sku' => 'SK2P',
                'category_id' => 5,
                'supplier_id' => 1,
                'price' => 120,
                'stock_alert' => 10,
                'description' => 'Safe electric socket',
            ],

            // -- 15
            [
                'name' => 'Socket 3 Pin',
                'sku' => 'SK3P',
                'category_id' => 5,
                'supplier_id' => 1,
                'price' => 180,
                'stock_alert' => 10,
                'description' => 'Heavy duty socket',
            ],

            // -- 16
            [
                'name' => 'MCB 32A',
                'sku' => 'MCB32',
                'category_id' => 6,
                'supplier_id' => 1,
                'price' => 480,
                'stock_alert' => 8,
                'description' => 'Reliable circuit breaker',
            ],

            // -- 17
            [
                'name' => 'MCB 63A',
                'sku' => 'MCB63',
                'category_id' => 6,
                'supplier_id' => 1,
                'price' => 780,
                'stock_alert' => 8,
                'description' => 'Premium circuit breaker',
            ],

            // -- 18
            [
                'name' => 'Wire 1.5 RM',
                'sku' => 'WR15',
                'category_id' => 1,
                'supplier_id' => 1,
                'price' => 2400,
                'stock_alert' => 5,
                'description' => 'Copper electric wire',
            ],

            // -- 19
            [
                'name' => 'Wire 2.5 RM',
                'sku' => 'WR25',
                'category_id' => 1,
                'supplier_id' => 1,
                'price' => 3600,
                'stock_alert' => 5,
                'description' => 'High quality wire',
            ],

            // -- 20
            [
                'name' => 'Wire 4 RM',
                'sku' => 'WR4',
                'category_id' => 1,
                'supplier_id' => 1,
                'price' => 5200,
                'stock_alert' => 5,
                'description' => 'Industrial copper wire',
            ],

            // -- continue up to 53

            // -- 21
            [
                'name' => 'Water Pump 1HP',
                'sku' => 'WP1HP',
                'category_id' => 7,
                'supplier_id' => 1,
                'price' => 6800,
                'stock_alert' => 3,
                'description' => 'Powerful water pump',
            ],

            // -- 22
            [
                'name' => 'Water Pump 2HP',
                'sku' => 'WP2HP',
                'category_id' => 7,
                'supplier_id' => 1,
                'price' => 9200,
                'stock_alert' => 3,
                'description' => 'Heavy duty pump',
            ],

            // -- 23
            [
                'name' => 'Drill Machine 500W',
                'sku' => 'DM500',
                'category_id' => 8,
                'supplier_id' => 1,
                'price' => 3500,
                'stock_alert' => 4,
                'description' => 'Electric drill machine',
            ],

            // -- 24
            [
                'name' => 'Hammer',
                'sku' => 'HAM01',
                'category_id' => 8,
                'supplier_id' => 1,
                'price' => 450,
                'stock_alert' => 10,
                'description' => 'Steel hammer',
            ],

            // -- 25
            [
                'name' => 'Screw Driver Set',
                'sku' => 'SDS01',
                'category_id' => 8,
                'supplier_id' => 1,
                'price' => 650,
                'stock_alert' => 8,
                'description' => 'Multi screwdriver set',
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
