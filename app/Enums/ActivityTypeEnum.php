<?php

namespace App\Enums;

enum ActivityTypeEnum: string
{
    case BILL_CREATED = 'bill_created';
    case BILL_UPDATED = 'bill_updated';
    case PAYMENT_LOGGED = 'payment_logged';
    case PAYMENT_DELETED = 'payment_deleted';

    public function label(): string
    {
        return match ($this) {
            self::BILL_CREATED => 'Bill created',
            self::BILL_UPDATED => 'Bill updated',
            self::PAYMENT_LOGGED => 'Payment logged',
            self::PAYMENT_DELETED => 'Payment deleted',
        };
    }
}
