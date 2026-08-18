<?php

namespace App\Services;

use App\Http\Resources\ActivityResource;
use App\Models\ActivityModel;
use Illuminate\Http\JsonResponse;

class ActivityService extends BaseCrudService
{
    private const DEFAULT_PER_PAGE = 10;

    /**
     * Activity feed for a bill, latest first, length-aware paginated.
     *
     * @param  int  $billId  [explicite description]
     * @param array{
     *     page?: int,
     *     per_page?: int
     * } $params
     */
    public function activityList(int $billId, array $params = []): JsonResponse
    {
        $perPage = $params['per_page'] ?? self::DEFAULT_PER_PAGE;

        $paginator = ActivityModel::query()
            ->forBill($billId)
            ->paginate($perPage, ['*'], 'page', $params['page'] ?? 1);

        return $this->successMessage('Successfully fetched list.', [
            'items' => ActivityResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
