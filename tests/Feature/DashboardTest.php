<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    const API_URL = '/api/dashboard/summary';

    private function authCookie(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        return ['auth-token' => $token];
    }

    public function test_summary_requires_authentication(): void
    {
        $response = $this->getJson(self::API_URL);
        $response->assertStatus(401);
    }

    public function test_summary_returns_dashboard_data(): void
    {
        $response = $this->withCredentials()
            ->withUnencryptedCookies($this->authCookie())
            ->getJson(self::API_URL);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'year',
                'monthly_expenses',
                'bills_by_category',
                'upcoming_bills',
            ],
        ]);
        $this->assertCount(12, $response->json('data.monthly_expenses'));
    }
}
