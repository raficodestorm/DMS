<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $branches = [
            [
                'name' => 'Head Office',
                'manager' => 'Abdul Hakim Tanvir',
                'address' => 'Mistri Para, Dewanhat, Chattogram',
            ],
            [
                'name' => 'Jubilee Road',
                'manager' => 'Anwarul Alam Sakib',
                'address' => 'New Market, Chattogram',
            ],
        ];

        foreach ($branches as $branch) {

            Branch::updateOrCreate(
                ['name' => $branch['name']],
                $branch
            );
        }
    }
}
