<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Restricts a model's queries to rows created by users sharing the
 * authenticated user's group_code, so household members see the same
 * bills, transactions, and daily budgets.
 */
class GroupScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $builder->whereIn(
            $model->qualifyColumn('user_id'),
            User::query()->where('group_code', Auth::user()->group_code)->select('id')
        );
    }
}
