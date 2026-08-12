<?php

namespace App\Models;

use App\Enums\BillCategoryEnum;
use App\Enums\BillStatusEnum;
use App\Enums\PaymentTypesEnum;
use App\Services\BillService;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class BillsModel extends Model
{
    use HasFactory;

    protected $table = 'bills';

    protected $fillable = [
        'name',
        'amount',
        'billing_date',
        'end_date',
        'status',
        'category',
        'is_autopay',
        'description',
        'frequency',
        'default_payment',
    ];

    protected $casts = [
        'status' => BillStatusEnum::class,
        'category' => BillCategoryEnum::class,
        'default_payment' => PaymentTypesEnum::class,
        'billing_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = [
        'next_date_at',
    ];

    #[Override]
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    protected function nextDateAt(): Attribute
    {
        return Attribute::make(
            get: fn () => BillService::billingDate($this)
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(TransactionsModel::class, 'id', 'bills_id');
    }
}
