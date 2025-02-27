<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->withoutMiddleware();
    }

    public function testNameRequired()
    {
        $response = $this->post(route('register'), [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $this->followRedirects($response)->assertSessionHasErrors('name');
    }

    public function testEmailRequired()
    {
        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $this->followRedirects($response)->assertSessionHasErrors('email');
    }

    public function testPasswordRequired()
    {
        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect();

        $this->followRedirects($response)->assertSessionHasErrors('password');
    }

    public function testPasswordMin()
    {
        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ])->assertRedirect();

        $this->followRedirects($response)->assertSessionHasErrors('password');
    }

    public function testPasswordMatch()
    {
        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ])->assertRedirect();

        $this->followRedirects($response)->assertSessionHasErrors('password');
    }

    public function testRegisterRedirect()
    {
        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

        $user = User::where('email', 'test@example.com')->first();
        $user->email_verified_at = now();
        $user->save();

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
    }
}