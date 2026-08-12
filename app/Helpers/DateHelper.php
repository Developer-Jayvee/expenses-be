<?php

namespace App\Helpers;

use App\Enums\BillFrequencyEnum;
use Carbon\Carbon;
use Exception;

class DateHelper
{
   public static function getFutureDate(string $currentDate, int $count = 1 ,BillFrequencyEnum $frequency): Carbon
   {
        $date = Carbon::parse($currentDate);
        if(!$date) {
            throw new Exception("Invalid parse date", 500);
        }
        switch ($frequency) {
            case BillFrequencyEnum::DAILY:
                return $date->addDays($count);
            case BillFrequencyEnum::MONTHLY:
                return $date->addMonths($count);
            case BillFrequencyEnum::YEARLY:
                return $date->addYears($count);
            default:
                return $date;
        }
   }
}
