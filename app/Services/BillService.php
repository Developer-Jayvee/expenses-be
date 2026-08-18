<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Enums\ActivityTypeEnum;
use App\Enums\BillFrequencyEnum;
use App\Enums\BillStatusEnum;
use App\Helpers\DateHelper;
use App\Http\Resources\BillResource;
use App\Models\BillsModel;
use App\Models\TransactionsModel;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BillService extends BaseCrudService
{
    public function __construct(
        private readonly ActivityLoggerInterface $activityLogger
    ) {}

    /**
     * Create a bill and record the creation in its activity feed.
     *
     * @param  array<string, mixed>  $data
     */
    public function createBill(array $data): JsonResponse
    {
        DB::beginTransaction();
        try {
            $bill = BillsModel::query()->create($data);

            $this->activityLogger->log(
                $bill,
                ActivityTypeEnum::BILL_CREATED,
                "Bill \"{$bill->name}\" created."
            );

            DB::commit();

            return $this->successMessage('Successfully created.', new BillResource($bill->fresh()));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update a bill and record the change in its activity feed.
     *
     * @param  int  $id  [explicite description]
     * @param  array<string, mixed>  $data
     */
    public function updateBill(int $id, array $data): JsonResponse
    {
        DB::beginTransaction();
        try {
            $bill = BillsModel::query()->findOrFail($id);
            $bill->update($data);

            $this->activityLogger->log(
                $bill,
                ActivityTypeEnum::BILL_UPDATED,
                "Bill \"{$bill->name}\" updated."
            );

            DB::commit();

            return $this->successMessage('Successfully updated.', new BillResource($bill->fresh()));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Delete a bill.
     *
     * @param  int  $id  [explicite description]
     */
    public function deleteBill(int $id): JsonResponse
    {
        $bill = BillsModel::query()->findOrFail($id);
        $bill->delete();

        return $this->successMessage('Successfully deleted.', []);
    }

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

        return $this->successMessage('Success', self::billingDate($bill));
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

    /**
     * Whether a payment that brings the bill's total logged payments to
     * `$paymentsCount` is the final one this bill will ever take.
     *
     * A `once` bill only ever takes a single payment. A recurring bill
     * (daily/monthly/yearly) is done once its next due date would land
     * past its `end_date`; with no `end_date` it recurs indefinitely and
     * never resolves on its own.
     *
     * @param  int  $paymentsCount  Total payments logged, including the one being evaluated.
     */
    public static function isFinalPayment(BillsModel $bill, int $paymentsCount): bool
    {
        $frequency = BillFrequencyEnum::from($bill->frequency);

        if ($frequency === BillFrequencyEnum::ONCE) {
            return true;
        }

        if (! $bill->end_date) {
            return false;
        }

        $nextDueDate = DateHelper::getFutureDate(
            currentDate: $bill->billing_date,
            count: $paymentsCount,
            frequency: $frequency
        );

        return $nextDueDate->gt(Carbon::parse($bill->end_date));
    }

    /**
     * Status a bill should carry given how many payments it has logged.
     *
     * `active` = no payments yet, `ongoing` = has payments but not the
     * final one, `completed` = the final payment has been logged.
     *
     * @param  int  $paymentsCount  Total payments currently logged for the bill.
     */
    public static function resolveStatus(BillsModel $bill, int $paymentsCount): BillStatusEnum
    {
        if ($paymentsCount === 0) {
            return BillStatusEnum::ACTIVE;
        }

        return self::isFinalPayment($bill, $paymentsCount)
            ? BillStatusEnum::COMPLETED
            : BillStatusEnum::ONGOING;
    }
}
