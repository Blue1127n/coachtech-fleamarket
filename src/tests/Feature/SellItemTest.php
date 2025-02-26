<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;

class SellItemTest extends TestCase
{
    use RefreshDatabase;

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
    }

    public function test_create_item()
    {
        $this->withoutMiddleware();

        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $testImagePath = base_path('tests/Fixtures/test-item.jpg');

        if (!File::exists($testImagePath)) {
            $this->fail("テスト用の画像が存在しません: {$testImagePath}");
        }

        $image = new UploadedFile(
            $testImagePath,
            'test-item.jpg',
            'image/jpeg',
            null,
            true
        );

        $category = Category::create(['name' => 'テストカテゴリ']);
        $condition = Condition::create(['condition' => '新品']);


        $response = $this->actingAs($user)->post(route('item.store'), [
            'name' => 'テスト商品',
            'description' => 'テスト説明',
            'price' => 1000,
            'category' => $category->id,
            'condition' => $condition->id,
            'image' => $image,
            'status_id' => 1,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'description' => 'テスト説明',
            'price' => 1000,
            'condition_id' => $condition->id,
            'status_id' => 1,
        ]);

        Storage::disk('public')->assertExists('items/' . $image->hashName());
    }
}