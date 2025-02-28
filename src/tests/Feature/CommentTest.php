<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

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
                'status_id' => 1,
            ]
        );
    }

    public function testPostCommentAsUser()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('item.comment', ['item_id' => $this->item->id]), [
            'content' => 'これはテストコメントです。',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'content' => 'これはテストコメントです。',
        ]);
    }

    public function testGuestCannotComment()
    {
        $response = $this->post(route('item.comment', ['item_id' => $this->item->id]), [
            'content' => '未ログインのコメント',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function testEmptyCommentFails()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('item.comment', ['item_id' => $this->item->id]), [
            'content' => '',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['content']);
    }

    public function testLongCommentFails()
    {
        $this->actingAs($this->user);

        $longComment = str_repeat('あ', 256);

        $response = $this->postJson(route('item.comment', ['item_id' => $this->item->id]), [
            'content' => $longComment,
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['content']);
    }
}