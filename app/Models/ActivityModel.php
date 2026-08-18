<?php

namespace App\Models;

use App\Enums\ActivityTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityModel extends Model
{
    protected $table = 'activities';

    protected $fillable = [
        'bills_id',
        'user_id',
        'type',
        'description',
    ];

    protected $casts = [
        'type' => ActivityTypeEnum::class,
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(BillsModel::class, 'bills_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeForBill($query, int $billId)
    {
        return $query->where('bills_id', $billId)->orderByDesc('id');
    }
}
