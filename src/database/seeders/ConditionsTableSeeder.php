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
            ['id' => 1, 'condition' => '良好'],
            ['id' => 2, 'condition' => '目立った傷や汚れなし'],
            ['id' => 3, 'condition' => 'やや傷や汚れあり'],
            ['id' => 4, 'condition' => '状態が悪い'],
            ['id' => 5, 'condition' => '新品'],
            ['id' => 6, 'condition' => '未使用'],
            ['id' => 7, 'condition' => '中古'],
            ['id' => 8, 'condition' => 'ジャンク品'],
        ];

        foreach ($conditions as $condition) {
            DB::table('conditions')->updateOrInsert(
                ['id' => $condition['id']],
                ['condition' => $condition['condition'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}