<?php

namespace App\Services;

use App\Http\Resources\ChecklistGroupResource;
use App\Models\ChecklistGroupModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ChecklistGroupService extends BaseCrudService
{
    /**
     * List checklist groups scoped to the authenticated user's group.
     */
    public function listGroups(): JsonResponse
    {
        $groups = ChecklistGroupModel::query()
            ->withCount('items')
            ->latest('id')
            ->get();

        return $this->successMessage('Data fetched', ChecklistGroupResource::collection($groups));
    }

    /**
     * Create a checklist group along with its items.
     *
     * @param  array<string, mixed>  $data
     */
    public function createGroup(array $data): JsonResponse
    {
        DB::beginTransaction();
        try {
            $group = ChecklistGroupModel::query()->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            $group->items()->createMany($data['items']);

            DB::commit();

            return $this->successMessage('Successfully created.', new ChecklistGroupResource($group->fresh('items')));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update a checklist group's title/description and fully replace its items.
     *
     * @param  int  $id  [explicite description]
     * @param  array<string, mixed>  $data
     */
    public function updateGroup(int $id, array $data): JsonResponse
    {
        DB::beginTransaction();
        try {
            $group = ChecklistGroupModel::query()->findOrFail($id);

            $group->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            $group->items()->delete();
            $group->items()->createMany($data['items']);

            DB::commit();

            return $this->successMessage('Successfully updated.', new ChecklistGroupResource($group->fresh('items')));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Get a checklist group with its items.
     *
     * @param  int  $id  [explicite description]
     */
    public function getGroup(int $id): JsonResponse
    {
        $group = ChecklistGroupModel::with('items')->findOrFail($id);

        return $this->successMessage('Success', new ChecklistGroupResource($group));
    }

    /**
     * Delete a checklist group; its items cascade-delete via the FK.
     *
     * @param  int  $id  [explicite description]
     */
    public function deleteGroup(int $id): JsonResponse
    {
        $group = ChecklistGroupModel::query()->findOrFail($id);
        $group->delete();

        return $this->successMessage('Successfully deleted.', []);
    }
}
