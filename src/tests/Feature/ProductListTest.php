<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ConditionsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\StatusesTableSeeder;
use Database\Seeders\UsersTableSeeder;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
    parent::setUp();

    $this->seed(StatusesTableSeeder::class);
    $this->seed(ConditionsTableSeeder::class);
    $this->seed(CategoriesTableSeeder::class);
    $this->seed(UsersTableSeeder::class);
    $this->seed(ItemsTableSeeder::class);

    $this->availableStatusId = DB::table('statuses')->where('name', 'available')->value('id');
    $this->soldStatusId = DB::table('statuses')->where('name', 'sold')->value('id');

    $user = User::factory()->create([
        'email_verified_at' => now(),
        'postal_code' => '1234567',
        'address' => '東京都渋谷区1-3',
    ]);

    $this->actingAs($user);
    session()->regenerate();

    $response = $this->get(route('products.index'));
    }

    public function testProducts()
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);

        $totalItems = Item::count();
        $this->assertEquals($totalItems, Item::count());

        foreach (Item::all() as $item) {
            $response->assertSee($item->name);
        }
    }

    public function testSoldItems()
    {
    $response = $this->get(route('products.index'));

    $response->assertStatus(200);

    $soldItems = Item::where('status_id', $this->soldStatusId)->get();

    foreach ($soldItems as $item) {
        $response->assertSee($item->name);
        $response->assertSee('SOLD');
    }
    }

    public function testUserProducts()
    {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'postal_code' => '1234567',
        'address' => '東京都渋谷区1-3',
    ]);

    $this->actingAs($user);

    $userItems = Item::where('user_id', $user->id)->get();

    $response = $this->get(route('products.index'));

    $response->assertStatus(200);

    foreach ($userItems as $item) {
        $response->assertDontSee($item->name);
    }
    }
}