<?php

namespace Database\Seeders;

use App\Contracts\GroupCodeGeneratorInterface;
use App\Enums\BillCategoryEnum;
use App\Enums\BillFrequencyEnum;
use App\Enums\BillStatusEnum;
use App\Enums\DailyBudgetStatusEnum;
use App\Enums\DailyExpenseTypeEnum;
use App\Enums\PaymentTypesEnum;
use App\Models\BillsModel;
use App\Models\DailyBudgetsModel;
use App\Models\DailyExpensesModel;
use App\Models\TransactionsModel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * `WithoutModelEvents` suppresses Eloquent model events for this run,
     * so the `creating` hook that stamps `user_id` (from `BelongsToGroup`)
     * never fires here — every group-scoped model below has its `user_id`
     * set explicitly before `save()`.
     */
    public function run(): void
    {
        $groupCode = app(GroupCodeGeneratorInterface::class)->generate();

        $userA = User::factory()->create([
            'email' => 'test@example.com',
            'group_code' => $groupCode,
        ]);

        User::factory()->create([
            'email' => 'partner@example.com',
            'group_code' => $groupCode,
        ]);

        User::factory()->create([
            'email' => 'other@example.com',
        ]);

        $bill = BillsModel::factory()->make([
            'name' => 'Electricity Bill',
            'amount' => 1500,
            'billing_date' => now()->toDateString(),
            'status' => BillStatusEnum::ACTIVE,
            'category' => BillCategoryEnum::UTILITIES,
            'frequency' => BillFrequencyEnum::MONTHLY,
            'default_payment' => PaymentTypesEnum::GCASH,
        ]);
        $bill->user_id = $userA->id;
        $bill->save();

        $transaction = new TransactionsModel([
            'bills_id' => $bill->id,
            'payment_mode' => PaymentTypesEnum::GCASH,
            'amount' => 1500,
            'order' => 1,
            'transaction_date' => now()->toDateString(),
        ]);
        $transaction->user_id = $userA->id;
        $transaction->save();

        $dailyBudget = new DailyBudgetsModel([
            'name' => 'Groceries',
            'budget_amount' => 500,
            'status' => DailyBudgetStatusEnum::ACTIVE,
            'budget_date' => now()->toDateString(),
        ]);
        $dailyBudget->user_id = $userA->id;
        $dailyBudget->save();

        $dailyExpense = new DailyExpensesModel([
            'daily_budget_id' => $dailyBudget->id,
            'name' => 'Grocery run',
            'type' => DailyExpenseTypeEnum::FOOD,
            'amount' => 150,
            'payment_type' => PaymentTypesEnum::CASH,
        ]);
        $dailyExpense->save();
    }
}
