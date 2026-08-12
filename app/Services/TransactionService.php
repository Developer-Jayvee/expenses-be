<?php

namespace App\Services;

use App\Models\BillsModel;
use App\Models\TransactionsModel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService extends BaseCrudService
{
    /**
     * Create Transaction
     *
     * @param  string  $billId  The bill to update.
     * @param array{
     *     payment_mode: string,
     *     transaction_date: string,
     *      notes : string,
     *     amount: float
     * } $data The data used to update the bill.
     */
    public function createTransaction(string $billId, array $data): JsonResponse
    {
        try {
            $bills = BillsModel::findOrFail($billId);

            if (! $bills) {
                throw new \Exception('Invalid bill id', 403);
            }
            if ($bills->amount > $data['amount']) {
                throw new \Exception('Invalid amount.', 403);
            }
            $startingDate = Carbon::parse($bills->billing_date);
            $transactionDate = Carbon::parse($data['transaction_date']);

            if ($transactionDate->lt($startingDate)) {
                throw new \Exception('Invalid transaction date', 403);
            }

            DB::beginTransaction();

            $change = abs((float) $bills->amount - $data['amount']);
            $count = self::countTransaction($bills->id) + 1;

            $transaction = TransactionsModel::query()->create([
                'order' => $count,
                'bills_id' => $bills->id,
                'notes' => $data['notes'],
                'payment_mode' => $data['payment_mode'],
                'transaction_date' => $data['transaction_date'] ?? now(),
                'amount' => $data['amount'],
                'change' => $change ?? 0,
            ]);

            DB::commit();

            return $this->successMessage('Successfully log', $transaction);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Transaction count per expenses
     *
     * @param  int  $billId  [explicite description]
     */
    private static function countTransaction(int $billId): int
    {
        return TransactionsModel::transactions($billId)->count();
    }
}
