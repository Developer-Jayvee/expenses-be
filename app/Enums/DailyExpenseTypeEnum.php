<?php

namespace App\Enums;

enum DailyExpenseTypeEnum: string
{
    case FOOD = 'food';
    case UTILITIES = 'utilities';
    case GAS = 'gas';
    case TRANSPORT_FEE = 'transport_fee';

    public function label(): string
    {
        return match ($this) {
            self::FOOD => 'Food',
            self::UTILITIES => 'Utilities',
            self::GAS => 'Gas',
            self::TRANSPORT_FEE => 'Transport Fee',
        };
    }
}
