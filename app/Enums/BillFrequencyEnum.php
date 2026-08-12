<?php

namespace App\Enums;

enum BillFrequencyEnum: string
{
    case DAILY = "daily";
    case YEARLY = "yearly";
    case MONTHLY = "monthly";
    case ONCE = "once";
}
