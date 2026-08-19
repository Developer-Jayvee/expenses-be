<?php

namespace Tests\Feature;

use App\Enums\BillStatusEnum;
use App\Enums\DailyExpenseTypeEnum;
use App\Enums\PaymentTypesEnum;
use App\Models\BillsModel;
use App\Models\DailyBudgetsModel;
use App\Models\DailyExpensesModel;
use App\Models\TransactionsModel;
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

    public function test_monthly_expenses_includes_daily_expenses(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $this->actingAs($user);

        $now = now();

        $bill = BillsModel::factory()->create();

        TransactionsModel::create([
            'bills_id' => $bill->id,
            'payment_mode' => PaymentTypesEnum::CASH,
            'amount' => 150,
            'order' => 1,
            'transaction_date' => $now,
        ]);

        $budget = DailyBudgetsModel::create([
            'name' => 'Groceries',
            'budget_amount' => 500,
            'budget_date' => $now,
        ]);

        DailyExpensesModel::create([
            'daily_budget_id' => $budget->id,
            'name' => 'Lunch',
            'type' => DailyExpenseTypeEnum::FOOD,
            'amount' => 75.50,
            'payment_type' => PaymentTypesEnum::CASH,
        ]);

        $response = $this->withCredentials()
            ->withUnencryptedCookies(['auth-token' => $token])
            ->getJson(self::API_URL);

        $response->assertStatus(200);

        $monthly = collect($response->json('data.monthly_expenses'))
            ->firstWhere('month', (int) $now->format('n'));

        $this->assertEquals(225.50, $monthly['total']);
    }

    public function test_summary_skips_active_bills_with_no_frequency(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $this->actingAs($user);

        BillsModel::factory()->create(['status' => BillStatusEnum::ACTIVE]);

        $response = $this->withCredentials()
            ->withUnencryptedCookies(['auth-token' => $token])
            ->getJson(self::API_URL);

        $response->assertStatus(200);
        $this->assertSame([], $response->json('data.upcoming_bills'));
    }
}
