<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItemModel extends Model
{
    use HasFactory;

    protected $table = 'checklist_items';

    protected $fillable = [
        'checklist_group_id',
        'item_name',
        'estimated_price',
        'quantity',
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ChecklistGroupModel::class, 'checklist_group_id');
    }
}
