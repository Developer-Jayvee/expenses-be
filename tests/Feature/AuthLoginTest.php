<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    public function test_user_login(): void
    {
        $response = $this->post('api/login',[
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        $response->assertStatus(200);
        $response->assertCookie('auth-token');
    }
}
