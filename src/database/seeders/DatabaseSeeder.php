<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            StatusesTableSeeder::class,
            CategoriesTableSeeder::class,
            UsersTableSeeder::class,
            ItemsTableSeeder::class,
            TransactionsTableSeeder::class,
            ItemCategoryTableSeeder::class,
            ]);
    }
}
