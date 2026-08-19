<?php

namespace App\Enums;

enum DashboardPeriodEnum: string
{
    case MONTHLY = 'monthly';
    case WEEKLY = 'weekly';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Monthly',
            self::WEEKLY => 'Weekly',
        };
    }
}
