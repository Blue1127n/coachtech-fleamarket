<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'name' => '腕時計',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'condition' => '良好',
                'image' => 'storage/items/Armani+Mens+Clock.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'HDD',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'condition' => '目立った傷や汚れなし',
                'image' => 'storage/items/HDD+Hard+Disk.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => '玉ねぎ3束',
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'condition' => 'やや傷や汚れあり',
                'image' => 'storage/items/iLoveIMG+d.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => '革靴',
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'condition' => '状態が悪い',
                'image' => 'storage/items/Leather+Shoes+Product+Photo.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'ノートPC',
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'condition' => '良好',
                'image' => 'storage/items/Living+Room+Laptop.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'マイク',
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'condition' => '目立った傷や汚れなし',
                'image' => 'storage/items/Music+Mic+4632231.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'ショルダーバッグ',
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'condition' => 'やや傷や汚れあり',
                'image' => 'storage/items/Purse+fashion+pocket.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'タンブラー',
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'condition' => '状態が悪い',
                'image' => 'storage/items/Tumbler+souvenir.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'コーヒーミル',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'condition' => '良好',
                'image' => 'storage/items/Waitress+with+Coffee+Grinder.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'メイクセット',
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'condition' => '目立った傷や汚れなし',
                'image' => 'storage/items/外出メイクアップセット.jpg',
                'user_id' => 1,
                'status_id' => 1,
            ],
        ];

        foreach ($items as $item) {
            DB::table('items')->updateOrInsert(
                ['name' => $item['name']],
                array_merge($item, ['updated_at' => now()])
            );
        }
    }
}
