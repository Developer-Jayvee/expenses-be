<?php

namespace App\Enums;

enum BillStatusEnum: string
{
    case ACTIVE = 'active';
    case ONGOING = 'ongoing';
    case INACTIVE = 'inactive';
    case COMPLETED = 'completed';
}
