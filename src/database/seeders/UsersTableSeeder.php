<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'name' => 'テストユーザー',
                'email' => 'test@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'postal_code' => '1234567',
                'address' => '東京都渋谷区1-3',
                'building' => '渋谷ヒカリエ601',
                'profile_image' => null,
            ],
            [
                'id' => 2,
                'name' => 'テストユーザー2',
                'email' => 'test2@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'postal_code' => '2345678',
                'address' => '大阪府大阪市中央区2-4',
                'building' => 'なんばパークス301',
                'profile_image' => null,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
