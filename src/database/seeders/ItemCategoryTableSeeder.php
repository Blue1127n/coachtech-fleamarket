<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('item_category')->insert([
            ['item_id' => 1, 'category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 1, 'category_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 2, 'category_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 2, 'category_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 3, 'category_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 4, 'category_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 5, 'category_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 6, 'category_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 7, 'category_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 8, 'category_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 9, 'category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 10, 'category_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
