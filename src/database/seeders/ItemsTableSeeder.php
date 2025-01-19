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
        // conditions テーブルからデータを取得
        $conditions = DB::table('conditions')->pluck('id', 'condition'); // 'condition' => 'id'

        $items = [
            [
                'name' => '腕時計',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'condition_id' => $conditions['良好'],
                'image' => 'items/Armani+Mens+Clock.jpg',
                'brand' => 'Armani',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'HDD',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'condition_id' => $conditions['目立った傷や汚れなし'],
                'image' => 'items/HDD+Hard+Disk.jpg',
                'brand' => 'Western Digital',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => '玉ねぎ3束',
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'condition_id' => $conditions['やや傷や汚れあり'],
                'image' => 'items/iLoveIMG+d.jpg',
                'brand' => null,
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => '革靴',
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'condition_id' => $conditions['状態が悪い'],
                'image' => 'items/Leather+Shoes+Product+Photo.jpg',
                'brand' => 'ALDEN',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'ノートPC',
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'condition_id' => $conditions['良好'],
                'image' => 'items/Living+Room+Laptop.jpg',
                'brand' => 'Dell',
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'マイク',
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'condition_id' => $conditions['目立った傷や汚れなし'],
                'image' => 'items/Music+Mic+4632231.jpg',
                'brand' => null,
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'ショルダーバッグ',
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'condition_id' => $conditions['やや傷や汚れあり'],
                'image' => 'items/Purse+fashion+pocket.jpg',
                'brand' => null,
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'タンブラー',
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'condition_id' => $conditions['状態が悪い'],
                'image' => 'items/Tumbler+souvenir.jpg',
                'brand' => null,
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'コーヒーミル',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'condition_id' => $conditions['良好'],
                'image' => 'items/Waitress+with+Coffee+Grinder.jpg',
                'brand' => null,
                'user_id' => 1,
                'status_id' => 1,
            ],
            [
                'name' => 'メイクセット',
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'condition_id' => $conditions['目立った傷や汚れなし'],
                'image' => 'items/外出メイクアップセット.jpg',
                'brand' => null,
                'user_id' => 1,
                'status_id' => 1,
            ],
        ];

        foreach ($items as $item) {
            DB::table('items')->updateOrInsert(
                ['name' => $item['name']],
                array_merge($item, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
