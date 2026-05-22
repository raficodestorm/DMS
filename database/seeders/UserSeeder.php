<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
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
                'email' => 'relectricbdofficial@gmail.com',
                'password' => Hash::make('33333333'),
                'role' => 'admin',
                'branch_id' => 1,
                'status' => 'active',
                'timezone' => 'Asia/Dhaka',
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
