<?php

namespace App\Models;

use App\Enums\BillCategoryEnum;
use App\Enums\BillStatusEnum;
use App\Enums\PaymentTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillsModel extends Model
{
    use HasFactory;
    
    protected $table = "bills";

    protected $fillable = [
        'name', 'amount' , 'billing_date', 'end_date','status',
        'category' , 'is_autopay' , 'description' , 'frequency',
        'default_payment'
    ];

    protected $casts = [
        'status' => BillStatusEnum::class,
        'category' => BillCategoryEnum::class,
        'default_payment' => PaymentTypesEnum::class,
        'billing_date' => 'date',
        'end_date' => 'date'
    ];
}
