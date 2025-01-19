<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conditions = [
            ['condition' => '良好'],
            ['condition' => '目立った傷や汚れなし'],
            ['condition' => 'やや傷や汚れあり'],
            ['condition' => '状態が悪い'],
            ['condition' => '新品'],
            ['condition' => '未使用'],
            ['condition' => '中古'],
            ['condition' => 'ジャンク品'],
        ];

        DB::table('conditions')->insert($conditions);
    }
}
