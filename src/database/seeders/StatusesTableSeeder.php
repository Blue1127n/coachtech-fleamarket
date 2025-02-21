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
        $statuses = [
            ['id' => 1, 'name' => 'available', 'description' => '販売中'],
            ['id' => 2, 'name' => 'pending', 'description' => '取引保留中'],
            ['id' => 3, 'name' => 'completed', 'description' => '取引完了'],
            ['id' => 4, 'name' => 'cancelled', 'description' => '取引キャンセル'],
            ['id' => 5, 'name' => 'sold', 'description' => '売却済み'],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['id' => $status['id']],
                ['name' => $status['name'], 'description' => $status['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
