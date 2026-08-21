<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\BillsModel;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function index(BillsModel $bill, ListTransactionRequest $request): JsonResponse
    {
        try {
            if (! $bill) {
                throw new ValidationException('Invalid bill ID', 500);
            }

            return $this->transactionService->transactionList($bill->id, $request->validated());
        } catch (\Exception $err) {
            return $this->errorResponse($err);
        }
    }

    /**
     * Create Transaction
     *
     * @param  StoreTransactionRequest  $request  [explicite description]
     */
    public function createTransaction(StoreTransactionRequest $request): JsonResponse
    {
        try {
            return $this->transactionService->createTransaction(
                $request->validated('billsId'),
                [
                    'transaction_date' => $request->validated('transaction_date'),
                    'notes' => $request->validated('notes'),
                    'payment_mode' => $request->validated('payment_mode'),
                    'amount' => $request->validated('amount'),
                    'periods' => $request->validated('periods') ?? 1,
                ]
            );
        } catch (\Exception $ex) {
            return $this->errorResponse($ex);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->transactionService->deleteTransaction($id);
        } catch (\Exception $err) {
            return $this->errorResponse($err);
        }
    }
}
