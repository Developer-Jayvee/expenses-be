<?php

namespace App\Services;

use App\Models\BillsModel;
use App\Models\TransactionsModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TransactionService extends BaseCrudService
{
    /**
     * Create Transaction
     *
     * @param  BillsModel  $bills  The bill to update.
     * @param array{
     *     payment_mode: string,
     *     transaction_date: string,
     *      notes : string,
     *     amount: float
     * } $data The data used to update the bill.
     */
    public function createTransaction(string $billId, array $data): JsonResponse
    {   
        $bills = BillsModel::findOrFail($billId);

        if (! $bills) {
            throw new ValidationException('Invalid bill id', 403);
        }
        if($bills->amount > $data['amount']) {
            throw new ValidationException("Invalid amount.", 403);
        }

        $change = abs((float) $bills->amount - $data['amount']);
        
        $transactions = TransactionsModel::query()->create([
            'order' => self::countTransaction($bills->id) + 1,
            'bills_id' => $bills->id,
            'notes' => $data['notes'],
            'payment_mode' => $data['payment_mode'],
            'transaction_date' => $data['transaction_date'] ?? now(),
            'amount' => $data['amount'],
            'change' => $change ?? 0
        ]);
        return $this->successMessage('Successfully log', $transactions);
    }
        
    /**
     * Transaction count per expenses
     *
     * @param int $billId [explicite description]
     *
     * @return int
     */
    private static function countTransaction(int $billId): int 
    {
        return TransactionsModel::transactions($billId)->count();
    }
}
