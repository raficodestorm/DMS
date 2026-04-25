<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories = [
            [
                'name' => 'Light',
                'description' => 'This is valid category of RElectric',
            ],
            [
                'name' => 'Cable',
                'description' => 'This is valid category of RElectric',
            ],
            [
                'name' => 'Tape',
                'description' => 'This is valid category of RElectric',
            ],
            [
                'name' => 'Fan',
                'description' => 'This is valid category of RElectric',
            ],
            [
                'name' => 'Switch',
                'description' => 'This is valid category of RElectric',
            ],
            [
                'name' => 'Board',
                'description' => 'This is valid category of RElectric',
            ],
            [
                'name' => 'Plug',
                'description' => 'This is valid category of RElectric',
            ],
            [
                'name' => 'Socket',
                'description' => 'This is valid category of RElectric',
            ],
            [
                'name' => 'Log',
                'description' => 'This is valid category of RElectric',
            ],
        ];

        foreach ($categories as $category) {

            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
