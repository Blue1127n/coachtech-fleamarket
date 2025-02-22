<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class ShippingAddressTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テストユーザー',
                'password' => bcrypt('password'),
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区1-3',
                'building' => '渋谷ヒカリエ601',
            ]
        );

        $this->item = Item::create([
            'name' => 'テスト商品',
            'description' => 'テスト商品説明',
            'price' => 1000,
            'condition_id' => 1,
            'image' => 'items/default.jpg',
            'brand' => 'テストブランド',
            'user_id' => $this->user->id,
            'status_id' => 1,
        ]);

        Log::info("`setUp()` 実行完了", [
            'user' => $this->user->toArray(),
            'item' => $this->item->toArray()
        ]);
    }

    /** @test */
    public function testAddressChangeReflectsInPurchaseScreen()
    {
        $this->actingAs($this->user);

        // 送付先住所を変更
        $this->post(route('item.updateAddress', ['item_id' => $this->item->id]), [
            'postal_code' => '999-9999',
            'address' => '新しい住所1-2-3',
            'building' => '新しいビル101',
        ]);

        // **データを最新にリロード**
        $updatedTransaction = Transaction::where('item_id', $this->item->id)
            ->where('buyer_id', $this->user->id)
            ->first();

        if ($updatedTransaction) {
            $updatedTransaction = $updatedTransaction->fresh();
        }

        Log::info("`testAddressChangeReflectsInPurchaseScreen()`", [
            'updatedTransaction' => optional($updatedTransaction)->toArray()
        ]);

        $this->assertNotNull($updatedTransaction, "取引データが保存されていません");

        // **変更が適用されているか確認**
        $this->assertEquals('999-9999', $updatedTransaction->shipping_postal_code);
        $this->assertEquals('新しい住所1-2-3', $updatedTransaction->shipping_address);
        $this->assertEquals('新しいビル101', $updatedTransaction->shipping_building);

        // 購入ページへアクセス
        $response = $this->get(route('item.purchase', ['item_id' => $this->item->id]));

        // **デバッグ: HTML を確認**
        Log::info("購入ページの HTML", [
            'html' => $response->getContent()
        ]);

        // **変更後の住所が表示されるか確認**
        $response->assertSee('999-9999');
        $response->assertSee('新しい住所1-2-3');
        $response->assertSee('新しいビル101');
    }

    /** @test */
    public function testPurchasedItemHasUpdatedShippingAddress()
    {
        $this->actingAs($this->user);

        // 送付先住所を変更
        $this->post(route('item.updateAddress', ['item_id' => $this->item->id]), [
            'postal_code' => '999-9999',
            'address' => '新しい住所1-2-3',
            'building' => '新しいビル101',
        ]);

        Log::info("`testPurchasedItemHasUpdatedShippingAddress()` - 住所変更後", [
            'transactions' => Transaction::all()->toArray()
        ]);

        // **変更後の取引データを取得**
        $updatedTransaction = Transaction::where('item_id', $this->item->id)
            ->where('buyer_id', $this->user->id)
            ->first();

        if ($updatedTransaction) {
            $updatedTransaction = $updatedTransaction->fresh();
        }

        $this->assertNotNull($updatedTransaction, "住所変更後のデータが存在しません");

        // 商品を購入
        $this->post(route('item.processPurchase', ['item_id' => $this->item->id]), [
            'payment_method' => 'カード支払い',
        ]);

        Log::info("`testPurchasedItemHasUpdatedShippingAddress()` - 購入後", [
            'transactions' => Transaction::all()->toArray()
        ]);

        // **購入後の取引データを取得**
        $transaction = Transaction::where('item_id', $this->item->id)
                                    ->where('buyer_id', $this->user->id)
                                    ->first();

        if ($transaction) {
            $transaction = $transaction->fresh();
        }

        $this->assertNotNull($transaction, "購入後の取引データが保存されていません");
        $this->assertEquals('999-9999', $transaction->shipping_postal_code);
        $this->assertEquals('新しい住所1-2-3', $transaction->shipping_address);
        $this->assertEquals('新しいビル101', $transaction->shipping_building);
    }
}
