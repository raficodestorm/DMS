<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $users = [
            [
                'fullname' => 'Mr. Admin',
                'username' => 'admin',
                'email' => 'admin@system.com',
                'password' => Hash::make('11111111'),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'fullname' => 'Mr. Manager',
                'username' => 'manager',
                'email' => 'sarafi3258@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'manager',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],

        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
