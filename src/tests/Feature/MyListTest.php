<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Database\Seeders\StatusesTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\ItemsTableSeeder;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->soldStatusId = DB::table('statuses')->where('name', 'sold')->value('id');
        $this->availableStatusId = DB::table('statuses')->where('name', 'available')->value('id');

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '1234567',
            'address' => '東京都渋谷区1-3',
        ]);

        Item::create([
            'name' => 'テスト商品',
            'description' => 'テスト用の商品です。',
            'price' => 1000,
            'condition_id' => $this->availableStatusId,
            'status_id' => $this->availableStatusId,
            'image' => 'items/test.jpg',
            'brand' => 'TestBrand',
            'user_id' => $this->user->id,
        ]);
    }

    public function testLikedItems()
    {
        $likedItem = Item::first();
        $this->user->likes()->attach($likedItem->id);

        $this->actingAs($this->user);
        $response = $this->get(route('products.mylist'));

        $response->assertStatus(200);
        $response->assertSee($likedItem->name);
    }

    public function testSoldLabel()
    {
        $soldItem = Item::where('status_id', $this->soldStatusId)->first();
        $this->user->likes()->attach($soldItem->id);

        $this->actingAs($this->user);
        $response = $this->get(route('products.mylist'));

        $response->assertStatus(200);
        $response->assertSee($soldItem->name);
        $response->assertSee('SOLD');
    }

    public function testHideOwnItems()
    {
        $ownItem = Item::where('user_id', $this->user->id)->first();

        if (!$ownItem) {
            $this->fail('No item found for the logged-in user.');
        }

        $this->actingAs($this->user);
        $response = $this->get(route('products.mylist'));

        $response->assertStatus(200);
        $response->assertDontSee($ownItem->name);
    }

    public function testGuestSeesNothing()
    {
        $response = $this->get(route('products.mylist'));

        $response->assertStatus(200);
        $response->assertSee('ログインするとマイリストを表示できます');
    }
}