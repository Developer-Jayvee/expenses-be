<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Enums\ActivityTypeEnum;
use App\Enums\BillFrequencyEnum;
use App\Enums\BillStatusEnum;
use App\Helpers\DateHelper;
use App\Http\Resources\TransactionResource;
use App\Models\BillsModel;
use App\Models\TransactionsModel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransactionService extends BaseCrudService
{
    private const DEFAULT_PER_PAGE = 5;

    private const DEFAULT_SORT_BY = 'transaction_date';

    private const DEFAULT_SORT_DIR = 'desc';

    public function __construct(
        private readonly ActivityLoggerInterface $activityLogger
    ) {}

    /**
     * Transaction list for specific bill, length-aware paginated.
     *
     * @param  int  $billId  [explicite description]
     * @param array{
     *     page?: int,
     *     per_page?: int,
     *     sort_by?: string,
     *     sort_dir?: string
     * } $params
     */
    public function transactionList(int $billId, array $params = []): JsonResponse
    {
        $sortBy = $params['sort_by'] ?? self::DEFAULT_SORT_BY;
        $sortDir = $params['sort_dir'] ?? self::DEFAULT_SORT_DIR;
        $perPage = $params['per_page'] ?? self::DEFAULT_PER_PAGE;

        $paginator = TransactionsModel::transactions($billId)
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage, ['*'], 'page', $params['page'] ?? 1);

        return $this->successMessage('Successfully fetched list.', [
            'items' => TransactionResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
            'summary' => $this->transactionSummary($billId),
        ]);
    }

    /**
     * Aggregate totals across all of a bill's transactions, independent of
     * the current page, for KPI display.
     *
     * @param  int  $billId  [explicite description]
     */
    private function transactionSummary(int $billId): array
    {
        $totals = TransactionsModel::transactions($billId)
            ->selectRaw('COALESCE(SUM(amount), 0) as total_paid, COUNT(*) as payments_count')
            ->first();

        $lastPayment = TransactionsModel::transactions($billId)
            ->orderByDesc('transaction_date')
            ->first();

        return [
            'total_paid' => (float) $totals->total_paid,
            'payments_count' => (int) $totals->payments_count,
            'last_payment' => $lastPayment ? new TransactionResource($lastPayment) : null,
        ];
    }

    /**
     * Create Transaction
     *
     * When `periods` is more than 1, this logs one transaction per missed
     * billing period (e.g. selecting a date 2 months out from an unpaid
     * monthly bill logs 2 payments) rather than a single transaction dated
     * on the selected date. Every period uses the same amount/payment
     * mode/notes; only the final period is dated on the caller-supplied
     * `transaction_date` — earlier periods are dated on their own due date.
     *
     * @param  string  $billId  The bill to update.
     * @param array{
     *     payment_mode: string,
     *     transaction_date: string,
     *      notes : string,
     *     amount: float,
     *     periods?: int
     * } $data The data used to update the bill.
     */
    public function createTransaction(string $billId, array $data): JsonResponse
    {
        try {
            $bills = BillsModel::findOrFail($billId);

            if (! $bills) {
                throw new \Exception('Invalid bill id', 403);
            }
            if ($bills->status === BillStatusEnum::COMPLETED) {
                throw new \Exception('This bill has already been fully paid.', 403);
            }
            if ($bills->amount > $data['amount']) {
                throw new \Exception('Invalid amount.', 403);
            }
            $dueDate = BillService::billingDate($bills);
            $transactionDate = Carbon::parse($data['transaction_date']);

            if ($transactionDate->lt($dueDate)) {
                $message = TransactionsModel::transactions($bills->id)->exists()
                    ? 'You have already logged a payment for the current billing period.'
                    : 'Invalid transaction date';

                throw new \Exception($message, 403);
            }
            if ($bills->end_date && $transactionDate->gt(Carbon::parse($bills->end_date))) {
                throw new \Exception("Transaction date can't be later than the bill's end date.", 403);
            }

            $periods = max(1, (int) ($data['periods'] ?? 1));
            $frequency = BillFrequencyEnum::tryFrom($bills->frequency) ?? BillFrequencyEnum::ONCE;

            DB::beginTransaction();

            $change = abs((float) $bills->amount - $data['amount']);
            $nextOrder = self::nextOrder($bills->id);
            $paymentsCount = TransactionsModel::transactions($bills->id)->count();

            $transactions = [];
            for ($i = 0; $i < $periods; $i++) {
                $isLastPeriod = $i === $periods - 1;
                $periodDate = $isLastPeriod
                    ? $transactionDate
                    : DateHelper::getFutureDate($bills->billing_date, $paymentsCount + $i, $frequency);

                $transactions[] = TransactionsModel::query()->create([
                    'order' => $nextOrder + $i,
                    'bills_id' => $bills->id,
                    'notes' => $data['notes'],
                    'payment_mode' => $data['payment_mode'],
                    'transaction_date' => $periodDate,
                    'amount' => $data['amount'],
                    'change' => $change ?? 0,
                ]);

                // A client-supplied `periods` could otherwise overshoot a bill
                // that's about to be fully paid (e.g. a `once` bill, or a
                // recurring one nearing its `end_date`); stop as soon as this
                // period is the final one it can ever take.
                if (BillService::isFinalPayment($bills, $paymentsCount + $i + 1)) {
                    break;
                }
            }

            $loggedCount = count($transactions);
            $paymentsCount += $loggedCount;

            if ($bills->status !== BillStatusEnum::INACTIVE) {
                $bills->status = BillService::resolveStatus($bills, $paymentsCount);
                $bills->save();
            }

            $this->activityLogger->log(
                $bills,
                ActivityTypeEnum::PAYMENT_LOGGED,
                $loggedCount > 1
                    ? "{$loggedCount} payments of ".number_format((float) $data['amount'], 2).' logged.'
                    : 'Payment of '.number_format((float) $data['amount'], 2).' logged.'
            );

            DB::commit();

            return $this->successMessage('Successfully log', $transactions);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Soft delete a logged payment belonging to the current user.
     *
     * @param  int  $id  [explicite description]
     */
    public function deleteTransaction(int $id): JsonResponse
    {
        $transaction = TransactionsModel::query()->findOrFail($id);
        $bills = BillsModel::find($transaction->bills_id);

        $transaction->delete();

        if ($bills && $bills->status !== BillStatusEnum::INACTIVE) {
            $paymentsCount = TransactionsModel::transactions($bills->id)->count();
            $bills->status = BillService::resolveStatus($bills, $paymentsCount);
            $bills->save();
        }

        if ($bills) {
            $this->activityLogger->log(
                $bills,
                ActivityTypeEnum::PAYMENT_DELETED,
                'Payment of '.number_format((float) $transaction->amount, 2).' deleted.'
            );
        }

        return $this->successMessage('Successfully deleted', []);
    }

    /**
     * Next `order` value for a bill's transactions.
     *
     * Includes soft-deleted rows so a re-used `order` never collides with
     * the unique (bills_id, order) index once a payment has been deleted.
     *
     * @param  int  $billId  [explicite description]
     */
    private static function nextOrder(int $billId): int
    {
        return (int) TransactionsModel::withTrashed()->transactions($billId)->max('order') + 1;
    }
}
