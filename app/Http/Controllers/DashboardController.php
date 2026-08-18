<?php

namespace App\Http\Controllers;

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
}
