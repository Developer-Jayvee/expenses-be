<?php

namespace App\Services;

use App\Enums\DailyBudgetStatusEnum;
use App\Http\Resources\DailyBudgetResource;
use App\Models\DailyBudgetsModel;
use Illuminate\Http\JsonResponse;

class DailyBudgetService extends BaseCrudService
{
    /**
     * The current user's active daily-budget session, if any.
     */
    public function getActiveSession(): JsonResponse
    {
        $budget = DailyBudgetsModel::query()
            ->where('status', DailyBudgetStatusEnum::ACTIVE)
            ->with('expenses')
            ->first();

        return $this->successMessage(
            'Successfully fetched.',
            $budget ? new DailyBudgetResource($budget) : null
        );
    }

    /**
     * Start a new daily-budget session. Only one active session is allowed
     * per user at a time.
     *
     * @param  array{name: string, budget_amount: float}  $data
     */
    public function createSession(array $data): JsonResponse
    {
        $hasActiveSession = DailyBudgetsModel::query()
            ->where('status', DailyBudgetStatusEnum::ACTIVE)
            ->exists();

        if ($hasActiveSession) {
            throw new \Exception('You already have an active transaction.', 422);
        }

        $budget = DailyBudgetsModel::query()->create([
            'name' => $data['name'],
            'budget_amount' => $data['budget_amount'],
            'status' => DailyBudgetStatusEnum::ACTIVE,
            'budget_date' => now()->toDateString(),
        ]);

        return $this->successMessage('Successfully created.', new DailyBudgetResource($budget->fresh()));
    }

    /**
     * Past and present daily-budget sessions for the current user,
     * optionally filtered by name and/or date.
     *
     * @param  array{name?: string, date?: string}  $filters
     */
    public function listSessions(array $filters = []): JsonResponse
    {
        $query = DailyBudgetsModel::query();

        if (! empty($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        if (! empty($filters['date'])) {
            $query->whereDate('budget_date', $filters['date']);
        }

        $budgets = $query
            ->withCount('expenses')
            ->withSum('expenses', 'amount')
            ->orderByDesc('created_at')
            ->get();

        return $this->successMessage('Successfully fetched list.', DailyBudgetResource::collection($budgets));
    }

    /**
     * A single daily-budget session, with its expenses eager-loaded.
     *
     * @param  int  $id  [explicite description]
     */
    public function showSession(int $id): JsonResponse
    {
        $budget = DailyBudgetsModel::query()->with('expenses')->findOrFail($id);

        return $this->successMessage('Successfully fetched.', new DailyBudgetResource($budget));
    }

    /**
     * Mark an active daily-budget session as done.
     *
     * @param  int  $id  [explicite description]
     */
    public function markDone(int $id): JsonResponse
    {
        $budget = DailyBudgetsModel::query()->findOrFail($id);

        if ($budget->status !== DailyBudgetStatusEnum::ACTIVE) {
            throw new \Exception('Only an active transaction can be marked as done.', 422);
        }

        $budget->status = DailyBudgetStatusEnum::DONE;
        $budget->save();

        return $this->successMessage('Successfully updated.', new DailyBudgetResource($budget->fresh()));
    }

    /**
     * Cancel an active daily-budget session. Only allowed while the budget
     * is still untouched (no expenses logged yet).
     *
     * @param  int  $id  [explicite description]
     */
    public function cancelSession(int $id): JsonResponse
    {
        $budget = DailyBudgetsModel::query()->findOrFail($id);

        if ($budget->status !== DailyBudgetStatusEnum::ACTIVE) {
            throw new \Exception('Only an active transaction can be cancelled.', 422);
        }

        if ($budget->expenses()->exists()) {
            throw new \Exception('Cannot cancel — expenses have already been logged.', 422);
        }

        $budget->status = DailyBudgetStatusEnum::CANCELLED;
        $budget->save();

        return $this->successMessage('Successfully cancelled.', new DailyBudgetResource($budget->fresh()));
    }

    /**
     * Continue an overdue (in-progress) session into today: clears its
     * logged expenses and rolls its budget_date forward to today, keeping
     * it active.
     *
     * @param  int  $id  [explicite description]
     */
    public function continueSession(int $id): JsonResponse
    {
        $budget = DailyBudgetsModel::query()->findOrFail($id);

        if ($budget->status !== DailyBudgetStatusEnum::ACTIVE) {
            throw new \Exception('Only an active transaction can be continued.', 422);
        }

        if (! $budget->is_overdue) {
            throw new \Exception('Only an overdue transaction can be continued.', 422);
        }

        $budget->expenses()->delete();
        $budget->budget_date = now()->toDateString();
        $budget->save();

        return $this->successMessage('Successfully continued.', new DailyBudgetResource($budget->fresh('expenses')));
    }

    /**
     * Permanently delete a daily-budget session and its logged expenses.
     *
     * @param  int  $id  [explicite description]
     */
    public function deleteSession(int $id): JsonResponse
    {
        $budget = DailyBudgetsModel::query()->findOrFail($id);
        $budget->delete();

        return $this->successMessage('Successfully deleted.', null);
    }
}
