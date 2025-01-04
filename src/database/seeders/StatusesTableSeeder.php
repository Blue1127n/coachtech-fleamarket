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
            ['name' => 'available', 'description' => '販売中'],
            ['name' => 'pending', 'description' => '取引保留中'],
            ['name' => 'COMPLETED', 'description' => '取引完了'],
            ['name' => 'CANCELLED', 'description' => '取引キャンセル'],
            ['name' => 'sold', 'description' => '売却済み'],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['name' => $status['name']],
                ['description' => $status['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
