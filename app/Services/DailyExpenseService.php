<?php

namespace App\Services;

use App\Enums\DailyBudgetStatusEnum;
use App\Http\Resources\DailyBudgetResource;
use App\Models\DailyBudgetsModel;
use App\Models\DailyExpensesModel;
use Illuminate\Http\JsonResponse;

class DailyExpenseService extends BaseCrudService
{
    /**
     * Log an expense against an active daily-budget session and return the
     * refreshed session (with its expenses loaded) so the caller gets the
     * updated total in one round trip.
     *
     * @param  int  $budgetId  [explicite description]
     * @param array{
     *     name: string,
     *     type: string,
     *     amount: float,
     *     payment_type: string
     * } $data
     */
    public function addExpense(int $budgetId, array $data): JsonResponse
    {
        $budget = DailyBudgetsModel::query()->findOrFail($budgetId);

        if ($budget->status !== DailyBudgetStatusEnum::ACTIVE) {
            throw new \Exception('Expenses can only be logged against an active transaction.', 422);
        }

        DailyExpensesModel::query()->create([
            'daily_budget_id' => $budget->id,
            'name' => $data['name'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'payment_type' => $data['payment_type'],
        ]);

        return $this->successMessage('Successfully created.', new DailyBudgetResource($budget->fresh('expenses')));
    }

    /**
     * Delete a logged expense from its (still active) daily-budget session
     * and return the refreshed session.
     *
     * @param  int  $id  [explicite description]
     */
    public function deleteExpense(int $id): JsonResponse
    {
        $expense = DailyExpensesModel::whereHas('budget')->findOrFail($id);

        $budget = $expense->budget;

        if ($budget->status !== DailyBudgetStatusEnum::ACTIVE) {
            throw new \Exception('Expenses can only be deleted from an active transaction.', 422);
        }

        $expense->delete();

        return $this->successMessage('Successfully deleted.', new DailyBudgetResource($budget->fresh('expenses')));
    }
}
