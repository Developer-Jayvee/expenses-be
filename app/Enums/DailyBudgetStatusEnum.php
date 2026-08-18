<?php

namespace App\Enums;

enum DailyBudgetStatusEnum: string
{
    case ACTIVE = 'active';
    case DONE = 'done';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::DONE => 'Done',
            self::CANCELLED => 'Cancelled',
        };
    }
}
