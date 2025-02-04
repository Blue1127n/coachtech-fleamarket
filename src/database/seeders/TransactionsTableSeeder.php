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
        $transactions = [
            [
                'item_id' => 1,
                'buyer_id' => 1,
                'status_id' => 1,
                'payment_method' => null,
                'shipping_postal_code' => '5321122',
                'shipping_address' => '大阪府大阪市東成区中央1-7',
                'shipping_building' => '太陽マンション201',
            ],
            [
                'item_id' => 2,
                'buyer_id' => 2,
                'status_id' => 1,
                'payment_method' => null,
                'shipping_postal_code' => '1000001',
                'shipping_address' => '東京都千代田区千代田1-1',
                'shipping_building' => NULL,
            ],
        ];

        foreach ($transactions as $transaction) {
            DB::table('transactions')->updateOrInsert(
                [
                    'buyer_id' => $transaction['buyer_id'],
                    'item_id' => $transaction['item_id'],
                ],
                array_merge($transaction, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
