<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $suppliers = [
            [
                'name' => 'Md. Rishad',
                'company_name' => 'Walton',
                'phone' => '01844122236',
                'email' => 'walton@gmail.com',
                'address' => 'Lalbag, Dhaka, Bangladesh',
            ],
            

        ];

        foreach ($suppliers as $supplier) {

            Supplier::updateOrCreate(
                ['company_name' => $supplier['company_name']],
                $supplier
            );
        }
    }
}
