<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->insert([
            [
                'buyer_id' => 1,
                'item_id' => 1,
                'status_id' => 4, // COMPLETED
                'payment_method' => 'Credit Card',
                'shipping_address' => '123 Test St, Tokyo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'buyer_id' => 2,
                'item_id' => 2,
                'status_id' => 5, // CANCELLED
                'payment_method' => 'PayPal',
                'shipping_address' => '456 Another St, Osaka',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

