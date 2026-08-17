<?php

namespace App\Services;

use App\Http\Resources\TransactionResource;
use App\Models\BillsModel;
use App\Models\TransactionsModel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransactionService extends BaseCrudService
{
    /**
     * Transaction list for specific bill
     *
     * @param  int  $billId  [explicite description]
     */
    public function transactionList(int $billId): JsonResponse
    {
        return $this->successMessage('Successfully fetched list.',
            TransactionResource::collection(
                TransactionsModel::transactions($billId)->get()
            )
        );
    }

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
            $nextOrder = self::nextOrder($bills->id);

            $transaction = TransactionsModel::query()->create([
                'order' => $nextOrder,
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
     * Soft delete a logged payment belonging to the current user.
     *
     * @param  int  $id  [explicite description]
     */
    public function deleteTransaction(int $id): JsonResponse
    {
        $transaction = TransactionsModel::ownedByUser()->findOrFail($id);

        $transaction->delete();

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
