<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_defaults()
    {
        $this->withoutMiddleware();

        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'profile_image' => 'profile_images/test.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-3',
            'building' => '渋谷ヒカリエ601',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage.profile'));

        $response->assertStatus(200);

        $response->assertSee('テストユーザー');
        $response->assertSee('profile_images/test.jpg');
        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区1-3');
        $response->assertSee('渋谷ヒカリエ601');
    }
}
