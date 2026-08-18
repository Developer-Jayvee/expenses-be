<?php

namespace App\Models;

use App\Enums\DailyBudgetStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Override;

class DailyBudgetsModel extends Model
{
    use HasFactory;

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

    #[Override]
    public static function booted()
    {
        static::creating(function ($budget) {
            $budget->user_id = Auth::user()->id;
        });
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(DailyExpensesModel::class, 'daily_budget_id')->latest('id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeOwnedByUser($query)
    {
        return $query->where('user_id', Auth::user()->id);
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
