<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListDailyBudgetsRequest;
use App\Http\Requests\StoreDailyBudgetRequest;
use App\Http\Resources\DailyBudgetResource;
use App\Models\DailyBudgetsModel;
use App\Services\DailyBudgetService;
use Illuminate\Http\JsonResponse;
use Override;

class DailyBudgetsController extends BaseCrudController
{
    public function __construct(
        protected DailyBudgetService $dailyBudgetService
    ) {
        parent::__construct();
    }

    #[Override]
    public function setupParams()
    {
        $this->baseModel = new DailyBudgetsModel;
        $this->resource = DailyBudgetResource::class;
    }

    public function index(?ListDailyBudgetsRequest $request = null): JsonResponse
    {
        try {
            return $this->dailyBudgetService->listSessions($request?->validated() ?? []);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function store(StoreDailyBudgetRequest $request): JsonResponse
    {
        try {
            return $this->dailyBudgetService->createSession($request->validated());
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function active(): JsonResponse
    {
        try {
            return $this->dailyBudgetService->getActiveSession();
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->dailyBudgetService->showSession($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function done(int $id): JsonResponse
    {
        try {
            return $this->dailyBudgetService->markDone($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            return $this->dailyBudgetService->cancelSession($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }
}
