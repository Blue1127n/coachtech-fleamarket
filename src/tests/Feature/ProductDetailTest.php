<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '1234567',
            'address' => '東京都渋谷区1-3',
        ]);

        $this->condition = Condition::first();

        $this->item = Item::create([
            'name' => 'テスト商品',
            'description' => 'テスト商品の説明です。',
            'price' => 5000,
            'brand' => 'テストブランド',
            'image' => 'items/test-item.jpg',
            'user_id' => $this->user->id,
            'condition_id' => $this->condition->id,
            'status_id' => 1,
        ]);

        $this->categories = Category::take(2)->get();
        $this->item->categories()->attach($this->categories->pluck('id'));

        $this->commentUser = User::factory()->create();
        Comment::create([
            'item_id' => $this->item->id,
            'user_id' => $this->commentUser->id,
            'content' => 'テストコメントです。',
        ]);
    }

    public function testProductDetails()
    {
        $response = $this->get(route('item.show', ['item_id' => $this->item->id]));

        $response->assertStatus(200);

        $response->assertSee($this->item->name);
        $response->assertSee($this->item->brand);
        $response->assertSee(number_format($this->item->price));
        $response->assertSee($this->item->description);
        $response->assertSee($this->item->condition->condition);

        $response->assertSee(asset('storage/' . $this->item->image));

        $response->assertSee('0');

        $response->assertSee('1');

        $response->assertSee($this->commentUser->name);
        $response->assertSee('テストコメントです。');
    }

    public function testProductCategories()
    {
        $response = $this->get(route('item.show', ['item_id' => $this->item->id]));

        $response->assertStatus(200);

        foreach ($this->categories as $category) {
            $response->assertSee($category->name);
        }
    }
}