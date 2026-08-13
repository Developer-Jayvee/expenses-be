<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillsRequest;
use App\Http\Requests\UpdateBillsRequest;
use App\Http\Resources\BillResource;
use App\Models\BillsModel;
use App\Services\BillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Override;

class BillsController extends BaseCrudController
{
    #[Override]
    public function setupParams()
    {
        $this->service = new BillService;
        $this->baseModel = new BillsModel();
        $this->resource = BillResource::class;  
    }
    public function store(StoreBillsRequest $request) : JsonResponse
    {
        $this->storeRequest = $request::class;
        return parent::storeQuery();
    }
    public function update(UpdateBillsRequest $request , int $id) : JsonResponse
    {
        $this->updateRequest = $request::class;
        return parent::updateQuery($request,$id);
    }
    public function show(int $id) : JsonResponse
    {
        $this->baseModelId = $id;
        return parent::showQuery();
    }
    public function destroy(int $id) : JsonResponse
    {
        $this->baseModelId = $id;
        return parent::destroyQuery();
    }
    
    public function getNextBill(BillsModel $bill)
    {
        try {
            return $this->service->getNextBill($bill);
        } catch (\Exception $err) {
            return $this->errorResponse($err);
        }
    }
}
