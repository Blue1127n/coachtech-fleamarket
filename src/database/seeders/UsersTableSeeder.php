<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'テストユーザー',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'postal_code' => '1234567',
                'address' => '東京都渋谷区1-3',
                'building' => '渋谷ヒカリエ601',
                'profile_image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
