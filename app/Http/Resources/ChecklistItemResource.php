<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistItemResource extends JsonResource
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
            'item_name' => $this->item_name,
            'estimated_price' => (float) $this->estimated_price,
            'quantity' => (int) $this->quantity,
            'line_total' => (float) $this->estimated_price * (int) $this->quantity,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
