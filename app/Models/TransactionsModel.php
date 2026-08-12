<?php

namespace App\Models;

use App\Enums\PaymentTypesEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionsModel extends Model
{
    protected $table = "transactions";

    protected $fillable = [
        'bills_id',
        'payment_mode',
        'amount',
        'change',
        'order',
        'notes',
        'transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'payment_mode' => PaymentTypesEnum::class
    ];


    public function bills(): BelongsTo
    {
        return $this->belongsTo(BillsModel::class, 'bills_id');
    }
    public function scopeTransactions($query, int $billId)
    {
        return $query->where('bills_id', $billId);
    }
}
