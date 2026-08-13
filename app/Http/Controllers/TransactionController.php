<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\BillsModel;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}
        
    public function index(BillsModel $bill): JsonResponse
    {
        try {
            if(!$bill) {
                throw new ValidationException("Invalid bill ID", 500);
            }
            return $this->transactionService->transactionList($bill->id);
        } catch (\Exception $err) {
            return $this->errorResponse($err);
        }
    }
    /**
     * Create Transaction
     *
     * @param StoreTransactionRequest $request [explicite description]
     *
     * @return JsonResponse
     */
    public function createTransaction(StoreTransactionRequest $request): JsonResponse
    {
        try {
            return $this->transactionService->createTransaction(
                $request->validated("billsId"),
                [
                    'transaction_date' => $request->validated('transaction_date'),
                    'notes' => $request->validated('notes'),
                    'payment_mode' => $request->validated('payment_mode'),
                    'amount' => $request->validated('amount')
                ]
            );
        } catch (\Exception $ex) {
            return $this->errorResponse($ex);
        }
    }
}
