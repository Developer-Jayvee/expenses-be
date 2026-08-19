<?php

namespace App\Http\Controllers;

use App\Enums\DashboardPeriodEnum;
use App\Http\Requests\ListDashboardExpensesRequest;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function summary(): JsonResponse
    {
        try {
            return $this->dashboardService->getSummary();
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function expenses(ListDashboardExpensesRequest $request): JsonResponse
    {
        try {
            $period = DashboardPeriodEnum::tryFrom($request->validated('period') ?? '')
                ?? DashboardPeriodEnum::MONTHLY;

            return $this->dashboardService->getExpenses($period);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }
}
