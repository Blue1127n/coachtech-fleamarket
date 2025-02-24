<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // ユーザーの作成
        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profile_images/test_image.png',
        ]);

        // 出品した商品の作成
        Item::factory(3)->create([
            'user_id' => $this->user->id,
        ]);

        // 購入した商品の作成
        $purchasedItems = Item::factory(2)->create();

        foreach ($purchasedItems as $item) {
            Transaction::create([
                'item_id' => $item->id,
                'buyer_id' => $this->user->id,
                'status_id' => 3,
                'payment_method' => 'カード支払い',
            ]);
        }
    }

    /** @test */
    public function test_user_profile_information_is_displayed_correctly()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('mypage', ['page' => 'sell']));

        $response->assertStatus(200)
                 ->assertSee('テストユーザー')
                 ->assertSee(asset('storage/profile_images/test_image.png'));

        // 出品した商品が表示されているか確認
        foreach ($this->user->items as $item) {
            $response->assertSee($item->name);
        }

        // 購入した商品のページも確認
        $response = $this->get(route('mypage', ['page' => 'buy']));

        $response->assertStatus(200);

        foreach ($this->user->purchasedItems as $item) {
            $response->assertSee($item->name);
        }
    }
}
