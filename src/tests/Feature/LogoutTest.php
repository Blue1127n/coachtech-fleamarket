<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testLogout()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertAuthenticated();

        $response = $this->post(route('logout'));

        Session::flush();

        $this->assertGuest('web');

        $response->assertRedirect('/');
    }
}