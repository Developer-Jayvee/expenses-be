<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyBudgetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'budget_amount' => $this->budget_amount,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'budget_date' => $this->budget_date?->format('Y-m-d'),
            'total_spent' => $this->total_spent,
            'remaining_budget' => $this->remaining_budget,
            'expenses_count' => $this->expenses_count ?? $this->expenses()->count(),
            'created_at' => $this->created_at?->toISOString(),
            'expenses' => DailyExpenseResource::collection($this->whenLoaded('expenses')),
        ];
    }
}
