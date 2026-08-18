<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyExpenseResource extends JsonResource
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
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'amount' => $this->amount,
            'payment_type' => $this->payment_type?->value,
            'payment_type_label' => $this->payment_type?->label(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
