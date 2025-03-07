<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            StatusesTableSeeder::class,
            ConditionsTableSeeder::class,
            CategoriesTableSeeder::class,
            UsersTableSeeder::class,
            ItemsTableSeeder::class,
            ItemCategoryTableSeeder::class,
            TransactionsTableSeeder::class,
            ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
