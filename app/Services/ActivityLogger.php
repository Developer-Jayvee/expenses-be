<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Enums\ActivityTypeEnum;
use App\Models\ActivityModel;
use App\Models\BillsModel;
use Illuminate\Support\Facades\Auth;

class ActivityLogger implements ActivityLoggerInterface
{
    public function log(BillsModel $bill, ActivityTypeEnum $type, string $description): void
    {
        ActivityModel::query()->create([
            'bills_id' => $bill->id,
            'user_id' => Auth::id(),
            'type' => $type,
            'description' => $description,
        ]);
    }
}
