<?php

namespace App\Services;

use App\Contracts\GroupCodeGeneratorInterface;
use App\Models\User;
use Illuminate\Support\Str;

class GroupCodeGeneratorService implements GroupCodeGeneratorInterface
{
    public function generate(): string
    {
        do {
            $code = Str::upper(Str::random(5));
        } while (User::query()->where('group_code', $code)->exists());

        return $code;
    }
}
