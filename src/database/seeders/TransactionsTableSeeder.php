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
                'status_id' => 4,
                'payment_method' => 'Credit Card',
                'shipping_address' => '123 Test St, Tokyo',
            ],
            [
                'item_id' => 2,
                'buyer_id' => 2,
                'status_id' => 5,
                'payment_method' => 'PayPal',
                'shipping_address' => '456 Another St, Osaka',
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
