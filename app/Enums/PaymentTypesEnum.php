<?php

namespace App\Enums;

enum PaymentTypesEnum : string
{
    case GCASH  = "gcash";
    case CASH  = "cash";

    public function label(): string
    {
        return match($this) {
            self::GCASH => 'GCASH',
            self::CASH => 'CASH'
        };
    }
}
