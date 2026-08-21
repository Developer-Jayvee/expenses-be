<?php

namespace App\Models;

use App\Models\Concerns\BelongsToGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistGroupModel extends Model
{
    use BelongsToGroup, HasFactory;

    protected $table = 'checklist_groups';

    protected $fillable = [
        'title',
        'description',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItemModel::class, 'checklist_group_id')->latest('id');
    }
}
