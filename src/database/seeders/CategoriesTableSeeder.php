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
            ['name' => 'ファッション'],
            ['name' => '家電'],
            ['name' => 'インテリア'],
            ['name' => 'レディース'],
            ['name' => 'メンズ'],
            ['name' => 'コスメ'],
            ['name' => '本'],
            ['name' => 'ゲーム'],
            ['name' => 'スポーツ'],
            ['name' => 'キッチン'],
            ['name' => 'ハンドメイド'],
            ['name' => 'アクセサリー'],
            ['name' => 'おもちゃ'],
            ['name' => 'ベビー・キッズ'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
