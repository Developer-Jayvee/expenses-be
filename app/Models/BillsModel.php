<?php

namespace App\Models;

use App\Enums\BillStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillsModel extends Model
{
    use HasFactory;
    
    protected $table = "bills";

    protected $fillable = [
        'name', 'amount' , 'billing_date', 'end_date','status'
    ];

    protected $casts = [
        'status' => BillStatusEnum::class,
        'billing_date' => 'date',
        'end_date' => 'date'
    ];
}
