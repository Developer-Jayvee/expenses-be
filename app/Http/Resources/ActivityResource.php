<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'bills_id' => $this->bills_id,
            'user' => $this->user,
            'type' => [
                'value' => $this->type->value,
                'label' => $this->type->label(),
            ],
            'description' => $this->description,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
