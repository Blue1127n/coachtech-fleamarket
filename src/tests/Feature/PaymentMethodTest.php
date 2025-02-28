<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $availableStatusId = \DB::table('statuses')->where('name', 'available')->value('id') ?? 1;

        $this->user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テストユーザー',
                'password' => bcrypt('password'),
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区1-3',
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
            'status_id' => $availableStatusId,
        ]);
    }

    public function testPaymentSelection()
    {
        $this->withoutMiddleware();

        $this->actingAs($this->user);

        $response = $this->get(route('item.purchase', ['item_id' => $this->item->id]));

        $response->assertStatus(200);
        $response->assertSee('支払い方法');
        $response->assertSee('未選択');

        $response = $this->postJson(route('item.processPurchase', ['item_id' => $this->item->id]), [
            'payment_method' => 'カード支払い',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-3',
            'building' => 'テストビル201'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => '支払い方法が選択されました',
        ]);

        $summaryResponse = $this->get(route('item.purchase', ['item_id' => $this->item->id]));
        $summaryResponse->assertSee('カード支払い');
    }
}