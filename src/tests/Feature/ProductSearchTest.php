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

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->item1 = Item::create([
            'name' => 'テストスマホ',
            'description' => '高性能なスマートフォン',
            'price' => 30000,
            'condition_id' => 1,
            'image' => 'items/test_phone.jpg',
            'brand' => 'Apple',
            'user_id' => 1,
            'status_id' => 1,
        ]);

        $this->item2 = Item::create([
            'name' => 'ノートパソコン',
            'description' => '軽量なノートPC',
            'price' => 50000,
            'condition_id' => 1,
            'image' => 'items/test_laptop.jpg',
            'brand' => 'Dell',
            'user_id' => 1,
            'status_id' => 1,
        ]);

        $this->item3 = Item::create([
            'name' => 'ゲーミングマウス',
            'description' => 'プロ仕様のゲーミングマウス',
            'price' => 7000,
            'condition_id' => 1,
            'image' => 'items/test_mouse.jpg',
            'brand' => 'Logitech',
            'user_id' => 1,
            'status_id' => 1,
        ]);

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '1234567',
            'address' => '東京都渋谷区1-3',
        ]);
    }

    public function testNameSearch()
    {
        $response = $this->get(route('products.index', ['search' => 'スマホ']));

        $response->assertStatus(200);
        $response->assertSee($this->item1->name);
        $response->assertDontSee($this->item2->name);
        $response->assertDontSee($this->item3->name);
    }

    public function testSearchInMyList()
    {
        $this->user->likes()->attach($this->item1->id);
        $this->user->likes()->attach($this->item2->id);

        $this->actingAs($this->user);
        $response = $this->get(route('products.mylist', ['search' => 'スマホ']));

        $response->assertStatus(200);
        $response->assertSee($this->item1->name);
        $response->assertDontSee($this->item2->name);
        $response->assertDontSee($this->item3->name);
    }
}