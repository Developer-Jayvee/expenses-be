<?php

namespace App\Models;

use App\Enums\DailyBudgetStatusEnum;
use App\Models\Concerns\BelongsToGroup;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyBudgetsModel extends Model
{
    use BelongsToGroup, HasFactory;

    protected $table = 'daily_budgets';

    protected $fillable = [
        'name',
        'budget_amount',
        'status',
        'budget_date',
    ];

    protected $casts = [
        'status' => DailyBudgetStatusEnum::class,
        'budget_date' => 'date',
    ];

    protected $appends = [
        'total_spent',
        'remaining_budget',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(DailyExpensesModel::class, 'daily_budget_id')->latest('id');
    }

    protected function totalSpent(): Attribute
    {
        return Attribute::make(
            get: fn () => array_key_exists('expenses_sum_amount', $this->attributes)
                ? (float) ($this->attributes['expenses_sum_amount'] ?? 0)
                : (float) $this->expenses()->sum('amount')
        );
    }

    protected function remainingBudget(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) $this->budget_amount - $this->total_spent
        );
    }
}
