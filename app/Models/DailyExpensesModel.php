<?php

namespace App\Models;

use App\Enums\DailyExpenseTypeEnum;
use App\Enums\PaymentTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyExpensesModel extends Model
{
    use HasFactory;

    protected $table = 'daily_expenses';

    protected $fillable = [
        'daily_budget_id',
        'name',
        'type',
        'amount',
        'payment_type',
    ];

    protected $casts = [
        'type' => DailyExpenseTypeEnum::class,
        'payment_type' => PaymentTypesEnum::class,
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(DailyBudgetsModel::class, 'daily_budget_id');
    }
}
