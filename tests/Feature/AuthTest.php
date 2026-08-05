<?php

namespace Tests\Feature;

use App\Enums\AuthEnum;
use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
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
    public function test_user_logout() : void 
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $token = $user->createToken(AuthEnum::TOKEN_NAME->value)->plainTextToken;
        $response = $this
                    ->actingAs($user,'sanctum')
                    // ->withCookie(AuthEnum::TOKEN_NAME->value,$token)
                    ->postJson("api/logout");
        $response->assertStatus(200);

        $response->assertCookieExpired(AuthEnum::TOKEN_NAME->value);
    }
}
