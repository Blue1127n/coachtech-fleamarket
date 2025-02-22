<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Status;
use App\Models\Like;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->availableStatusId = DB::table('statuses')->where('name', 'available')->value('id');

        $this->user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テストユーザー',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'postal_code' => '1234567',
                'address' => '東京都渋谷区1-3',
            ]
        );

        $this->item = Item::firstOrCreate(
            ['name' => 'テスト商品'],
            [
                'description' => 'これはテスト商品の説明です。',
                'price' => 1000,
                'condition_id' => 1,
                'image' => 'items/default.jpg',
                'brand' => 'テストブランド',
                'user_id' => $this->user->id,
                'status_id' => $this->availableStatusId ?: 1,
            ]
        );
    }

    public function testLike()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('item.like', ['item_id' => $this->item->id]));

        $response->assertStatus(200)
                ->assertJson([
                    'liked' => true,
                    'likeCount' => 1,
                ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);
    }

    public function testLikeIconChange()
    {
        $this->actingAs($this->user);

        $this->post(route('item.like', ['item_id' => $this->item->id]));

        $response = $this->get(route('item.show', ['item_id' => $this->item->id]));

        $response->assertSee('liked');
    }

    public function testUnlike()
    {
        $this->actingAs($this->user);

        $this->post(route('item.like', ['item_id' => $this->item->id]));

        $response = $this->post(route('item.like', ['item_id' => $this->item->id]));

        $response->assertStatus(200)
                ->assertJson([
                    'liked' => false,
                    'likeCount' => 0,
                ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);
    }
}