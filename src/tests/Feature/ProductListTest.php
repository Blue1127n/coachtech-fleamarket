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
}

    /**
     * 商品一覧が正しく取得できるか
     */
    public function testProducts()
    {
        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // ステータスコードが200（OK）であることを確認
        $response->assertStatus(200);

        // データベースに10件の商品があることを確認
        $this->assertEquals(10, Item::count());

        // ビューに商品が表示されていることを確認
        foreach (Item::all() as $item) {
            $response->assertSee($item->name);
        }
    }

    /**
     * 購入済みの商品に「SOLD」ラベルが表示されることを確認するテスト
     */
    public function testSoldItems()
{
    $response = $this->get('/');

    // 修正: IDを取得して比較
    $soldCount = Item::where('status_id', $this->soldStatusId)->count();
    $this->assertEquals(3, $soldCount);

    $response->assertSee('SOLD');
}

    /**
     * 自分が出品した商品が一覧に表示されないことを確認するテスト
     */
    public function testUserProducts()
{
    $user = User::factory()->create();
    $this->actingAs($user);

    $userItems = Item::where('user_id', $user->id)
        ->where('status_id', $this->availableStatusId)
        ->get();

    $response = $this->get('/');

    foreach ($userItems as $item) {
        $response->assertDontSee($item->name);
    }
}
}