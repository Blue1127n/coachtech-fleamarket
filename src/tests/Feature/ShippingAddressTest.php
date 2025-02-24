<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;
use Faker\Factory as FakerFactory;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $faker = FakerFactory::create('ja_JP');

        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => $faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'postal_code' => '1234567',
            'address' => '東京都渋谷区1-3',
            'building' => '渋谷ヒカリエ601',
        ]);

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
    }

    public function testAddressUpdatedInView()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('item.updateAddress', ['item_id' => $this->item->id]), [
            'postal_code' => '999-9999',
            'address' => '新しい住所1-2-3',
            'building' => '新しいビル101',
        ]);

        $response->assertRedirect(route('item.purchase', ['item_id' => $this->item->id]));

        $updatedTransaction = Transaction::where('item_id', $this->item->id)
            ->where('buyer_id', $this->user->id)
            ->first();

        $this->assertNotNull($updatedTransaction, "取引データが保存されていません");
        $this->assertEquals('999-9999', $updatedTransaction->shipping_postal_code);
        $this->assertEquals('新しい住所1-2-3', $updatedTransaction->shipping_address);
        $this->assertEquals('新しいビル101', $updatedTransaction->shipping_building);

        $response = $this->get(route('item.purchase', ['item_id' => $this->item->id]));

        $response->assertSee('999-9999');
        $response->assertSee('新しい住所1-2-3');
        $response->assertSee('新しいビル101');
    }

    public function testAddressSavedOnPurchase()
    {
        $this->actingAs($this->user);

        $this->post(route('item.updateAddress', ['item_id' => $this->item->id]), [
            'postal_code' => '999-9999',
            'address' => '新しい住所1-2-3',
            'building' => '新しいビル101',
        ]);

    $response = $this->post(route('item.processPurchase', ['item_id' => $this->item->id]), [
        'payment_method' => 'カード支払い',
        'postal_code' => '999-9999',
        'address' => '新しい住所1-2-3',
        'building' => '新しいビル101',
    ]);

    $response->assertStatus(200);

    $transaction = Transaction::where('item_id', $this->item->id)
        ->where('buyer_id', $this->user->id)
        ->first();

    $this->assertNotNull($transaction, "購入後の取引データが保存されていません");
    $this->assertEquals('999-9999', $transaction->shipping_postal_code);
    $this->assertEquals('新しい住所1-2-3', $transaction->shipping_address);
    $this->assertEquals('新しいビル101', $transaction->shipping_building);
    $this->assertEquals('カード支払い', $transaction->payment_method);
    }
}