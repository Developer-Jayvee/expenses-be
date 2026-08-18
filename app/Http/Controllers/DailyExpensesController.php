<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyExpenseRequest;
use App\Services\DailyExpenseService;
use Illuminate\Http\JsonResponse;

class DailyExpensesController extends Controller
{
    public function __construct(
        protected DailyExpenseService $dailyExpenseService
    ) {}

    public function store(int $budget, StoreDailyExpenseRequest $request): JsonResponse
    {
        try {
            return $this->dailyExpenseService->addExpense($budget, $request->validated());
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->dailyExpenseService->deleteExpense($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }
}
