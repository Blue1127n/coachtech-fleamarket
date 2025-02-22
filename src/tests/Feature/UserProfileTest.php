<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\StatusesTableSeeder;
use Database\Seeders\ConditionsTableSeeder;
use Illuminate\Support\Facades\Storage;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 必要なデータをSeederで準備
        $this->seed(StatusesTableSeeder::class);
        $this->seed(ConditionsTableSeeder::class);
        $this->seed(UsersTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        // ユーザー情報取得
        $this->user = DB::table('users')->where('id', 1)->first();
        $this->availableStatusId = DB::table('statuses')->where('name', 'available')->value('id');
        $this->soldStatusId = DB::table('statuses')->where('name', 'sold')->value('id');
    }

    /**
     * ユーザープロフィールページが正しく表示されることをテスト
     */
    public function testUserProfileDisplaysCorrectly()
    {
        // ログイン
        $this->actingAs(User::find($this->user->id));

        // プロフィールページにアクセス
        $response = $this->get(route('mypage'));

        // ステータスコード200を確認
        $response->assertStatus(200);

        // プロフィール画像、ユーザー名が表示されているか確認
        $response->assertSee($this->user->name);
        if ($this->user->profile_image) {
            $response->assertSee(asset('storage/' . $this->user->profile_image));
        }

        // 出品した商品一覧が表示されることを確認
        $sellingItems = DB::table('items')
            ->where('user_id', $this->user->id)
            ->where('status_id', $this->availableStatusId)
            ->get();

        foreach ($sellingItems as $item) {
            $response->assertSee($item->name);
        }

        // 購入した商品一覧が表示されることを確認
        $purchasedItems = DB::table('items')
            ->where('status_id', $this->soldStatusId)
            ->get();

        foreach ($purchasedItems as $item) {
            $response->assertSee($item->name);
        }
    }
}