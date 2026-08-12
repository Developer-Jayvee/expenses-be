<?php

namespace App\Services;

use App\Enums\BillFrequencyEnum;
use App\Helpers\DateHelper;
use App\Models\BillsModel;
use App\Models\TransactionsModel;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Http\JsonResponse;

class BillService extends BaseCrudService
{
    /**
     * Get next bill
     *
     * @param  BillsModel  $bill  [explicite description]
     */
    public function getNextBill(BillsModel $bill): JsonResponse
    {
        if (! $bill) {
            throw new \Exception('No bill found.', 404);
        }
        if (! $bill->billing_date) {
            throw new InvalidDateException('No date found under this bill', 403);
        }

        return $this->successMessage('Success' , self::billingDate($bill));
    }

    public static function billingDate(BillsModel $bill): Carbon
    {
        $paidBillCount = TransactionsModel::transactions($bill->id)->count();

        return DateHelper::getFutureDate(
            currentDate : $bill->billing_date,
            count: $paidBillCount,
            frequency: BillFrequencyEnum::from($bill->frequency)
        );
    }
}
