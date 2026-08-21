<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChecklistGroupRequest;
use App\Http\Requests\UpdateChecklistGroupRequest;
use App\Http\Resources\ChecklistGroupResource;
use App\Models\ChecklistGroupModel;
use App\Services\ChecklistGroupService;
use Illuminate\Http\JsonResponse;
use Override;

class ChecklistGroupsController extends BaseCrudController
{
    public function __construct(
        protected ChecklistGroupService $checklistGroupService
    ) {
        parent::__construct();
    }

    #[Override]
    public function setupParams()
    {
        $this->baseModel = new ChecklistGroupModel;
        $this->resource = ChecklistGroupResource::class;
    }

    public function index(): JsonResponse
    {
        try {
            return $this->checklistGroupService->listGroups();
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function store(StoreChecklistGroupRequest $request): JsonResponse
    {
        try {
            return $this->checklistGroupService->createGroup($request->validated());
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->checklistGroupService->getGroup($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function update(UpdateChecklistGroupRequest $request, int $id): JsonResponse
    {
        try {
            return $this->checklistGroupService->updateGroup($id, $request->validated());
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->checklistGroupService->deleteGroup($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }
    }
}
