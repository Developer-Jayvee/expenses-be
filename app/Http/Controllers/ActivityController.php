<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListActivityRequest;
use App\Models\BillsModel;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    public function __construct(
        protected ActivityService $activityService
    ) {}

    public function index(BillsModel $bill, ListActivityRequest $request): JsonResponse
    {
        try {
            return $this->activityService->activityList($bill->id, $request->validated());
        } catch (\Exception $err) {
            return $this->errorResponse($err);
        }
    }
}
