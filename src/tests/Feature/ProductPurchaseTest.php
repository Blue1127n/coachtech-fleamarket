<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed();

        $this->user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テストユーザー',
                'password' => bcrypt('password'),
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区1-3',
            ]
        );

        $this->availableStatusId = DB::table('statuses')->where('name', 'available')->value('id') ?: 1;
        $this->soldStatusId = DB::table('statuses')->where('name', 'sold')->value('id') ?: 5;

        $this->item = Item::firstOrCreate(
            ['name' => 'テスト商品'],
            [
                'description' => 'テスト商品説明',
                'price' => 1000,
                'condition_id' => 1,
                'image' => 'items/default.jpg',
                'brand' => 'テストブランド',
                'user_id' => $this->user->id,
                'status_id' => $this->availableStatusId,
            ]
        );
    }

    public function testItemPurchase()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('item.processPurchase', ['item_id' => $this->item->id]), [
            'payment_method' => 'カード支払い',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-3',
            'building' => 'テストビル201',
        ]);

        if ($response->status() === 500) {
            $this->fail('購入処理で500エラー発生: ' . json_encode($response->json(), JSON_PRETTY_PRINT));
        }

        $response->assertStatus(200);
        $response->assertJson([
            'message' => '支払い方法が選択されました',
        ]);

        $this->assertDatabaseHas('transactions', [
            'item_id' => $this->item->id,
            'buyer_id' => $this->user->id,
            'status_id' => 1,
            'payment_method' => 'カード支払い',
        ]);
    }

    public function testItemSoldStatus()
    {
        $this->actingAs($this->user);

        Transaction::updateOrCreate(
            ['item_id' => $this->item->id, 'buyer_id' => $this->user->id],
            [
                'status_id' => $this->soldStatusId,
                'payment_method' => 'カード支払い',
                'shipping_postal_code' => '123-4567',
                'shipping_address' => '東京都渋谷区1-3',
                'shipping_building' => 'テストビル201',
            ]
        );

        $response = $this->get(route('products.index'));

        $response->assertSee('sold');
    }

    public function testItemInProfile()
    {
        $this->actingAs($this->user);

        Transaction::updateOrCreate(
            ['item_id' => $this->item->id, 'buyer_id' => $this->user->id],
            [
                'status_id' => 5,
                'payment_method' => 'カード支払い',
                'shipping_postal_code' => '123-4567',
                'shipping_address' => '東京都渋谷区1-3',
                'shipping_building' => 'テストビル201',
            ]
        );

        $response = $this->get(route('mypage', ['page' => 'buy']));

        $response->assertStatus(200);
        $response->assertSee($this->item->name);
    }
}