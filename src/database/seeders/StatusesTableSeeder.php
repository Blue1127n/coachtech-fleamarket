<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('statuses')->insert([
            ['name' => 'available', 'description' => '購入可能'],
            ['name' => 'sold', 'description' => '売却済み'],
            ['name' => 'pending', 'description' => '取引中'],
            ['name' => 'COMPLETED', 'description' => '取引完了'],
            ['name' => 'CANCELLED', 'description' => '取引キャンセル'],
        ]);
    }
}
