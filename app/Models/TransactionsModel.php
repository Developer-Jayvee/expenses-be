<?php

namespace App\Models;

use App\Enums\PaymentTypesEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Override;

class TransactionsModel extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'bills_id',
        'user_id',
        'payment_mode',
        'amount',
        'change',
        'order',
        'notes',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'payment_mode' => PaymentTypesEnum::class,
    ];

    #[Override]
    public static function booted()
    {
        static::creating(function ($transaction) {
            $transaction->user_id = Auth::user()->id;
        });
    }

    public function bills(): BelongsTo
    {
        return $this->belongsTo(BillsModel::class, 'bills_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeTransactions($query, int $billId)
    {
        return $query->where([
            ['bills_id', $billId],
            ['user_id', Auth::user()->id],
        ]);
    }

    public function scopeOwnedByUser($query)
    {
        return $query->where('user_id', Auth::user()->id);
    }
}
