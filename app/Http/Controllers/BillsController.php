<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillsRequest;
use App\Http\Requests\UpdateBillsRequest;
use App\Http\Resources\BillResource;
use App\Models\BillsModel;
use App\Services\BillService;
use Illuminate\Http\JsonResponse;
use Override;

class BillsController extends BaseCrudController
{
    public function __construct(
        protected BillService $billService
    ) {
        parent::__construct();
    }

    #[Override]
    public function setupParams()
    {
        $this->baseModel = new BillsModel;
        $this->resource = BillResource::class;
    }

    public function store(StoreBillsRequest $request): JsonResponse
    {
        try {
            return $this->billService->createBill($request->validated());
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function update(UpdateBillsRequest $request, int $id): JsonResponse
    {
        try {
            return $this->billService->updateBill($id, $request->validated());
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function show(int $id): JsonResponse
    {
        $this->baseModelId = $id;

        return parent::showQuery();
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->billService->deleteBill($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function getNextBill(BillsModel $bill)
    {
        try {
            return $this->billService->getNextBill($bill);
        } catch (\Exception $err) {
            return $this->errorResponse($err);
        }
    }
}
