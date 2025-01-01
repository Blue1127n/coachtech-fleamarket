<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => '腕時計', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HDD', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '玉ねぎ3束', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '革靴', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ノートPC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'マイク', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ショルダーバッグ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'タンブラー', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'コーヒーミル', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'メイクセット', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
