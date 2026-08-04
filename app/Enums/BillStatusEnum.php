<?php

namespace App\Enums;

enum BillStatusEnum : string
{
    case ACTIVE = "active";
    case INACTIVE = "inactive";
    case COMPLETED = "completed";
}
