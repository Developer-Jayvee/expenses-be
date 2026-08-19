<?php

namespace App\Models\Concerns;

use App\Models\Scopes\GroupScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Applies the shared-household-group global scope and stamps the creator
 * `user_id` on new models, plus exposes the `user` relation to that
 * creator. Mixed into models that belong to a group (bills, transactions,
 * daily budgets).
 */
trait BelongsToGroup
{
    public static function bootBelongsToGroup(): void
    {
        static::addGlobalScope(new GroupScope);

        static::creating(function ($model) {
            $model->user_id ??= Auth::id();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
