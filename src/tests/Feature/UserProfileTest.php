<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $soldItem;
    protected $purchasedItem;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('statuses')->insertOrIgnore([
            ['id' => 1, 'name' => 'available', 'description' => '販売中', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'pending', 'description' => '取引保留中', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'completed', 'description' => '取引完了', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'cancelled', 'description' => '取引キャンセル', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'sold', 'description' => '売却済み', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('conditions')->insertOrIgnore([
            ['id' => 1, 'condition' => '良好', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'condition' => '目立った傷や汚れなし', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'condition' => 'やや傷や汚れあり', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'condition' => '状態が悪い', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'condition' => '新品', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Storage::fake('public');

        $sourceImagePath = base_path('tests/Fixtures/test.jpg');
        $destinationPath = storage_path('app/public/profile_images/test.jpg');

        if (File::exists($sourceImagePath)) {
            File::copy($sourceImagePath, $destinationPath);
        } else {
            File::put($destinationPath, str_repeat('0', 1024));
        }

        $this->user = User::create([
            'name' => 'テストユーザー',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
            'profile_image' => 'profile_images/test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-3',
            'building' => '渋谷ヒカリエ601',
        ]);

        $this->item = Item::create([
            'name' => '出品商品',
            'description' => '出品商品の説明',
            'price' => 1500,
            'condition_id' => 1,
            'image' => 'items/item1.jpg',
            'brand' => 'テストブランド',
            'user_id' => $this->user->id,
            'status_id' => 1,
        ]);

        $anotherUser = User::create([
            'name' => '別ユーザー',
            'email' => 'another@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->purchasedItem = Item::create([
            'name' => '購入商品',
            'description' => '購入商品の説明',
            'price' => 2000,
            'condition_id' => 1,
            'image' => 'items/item2.jpg',
            'brand' => 'テストブランド',
            'user_id' => $anotherUser->id,
            'status_id' => 5,
        ]);

        Transaction::create([
            'item_id' => $this->purchasedItem->id,
            'buyer_id' => $this->user->id,
            'status_id' => 3,
            'payment_method' => 'カード支払い',
            'shipping_postal_code' => '987-6543',
            'shipping_address' => '東京都新宿区1-1',
            'shipping_building' => '新宿ビル101',
        ]);
    }

    public function testProfilePage()
    {
        $this->user->markEmailAsVerified();

        $response = $this->actingAs($this->user)->get(route('mypage', ['page' => 'sell']));
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('profile_images/test.jpg');
        $response->assertSee('出品商品');
        $response->assertDontSee('購入商品');

        $responseBuy = $this->get(route('mypage', ['page' => 'buy']));
        $responseBuy->assertStatus(200);
        $responseBuy->assertSee('購入商品');
        $responseBuy->assertDontSee('出品商品');
    }
}