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
        $categories = [
            ['name' => '腕時計'],
            ['name' => 'HDD'],
            ['name' => '玉ねぎ3束'],
            ['name' => '革靴'],
            ['name' => 'ノートPC'],
            ['name' => 'マイク'],
            ['name' => 'ショルダーバッグ'],
            ['name' => 'タンブラー'],
            ['name' => 'コーヒーミル'],
            ['name' => 'メイクセット'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
