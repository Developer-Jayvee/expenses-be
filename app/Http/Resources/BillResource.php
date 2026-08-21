<?php

namespace App\Http\Resources;

use App\Enums\BillStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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
            'amount' => $this->amount,
            'billing_date' => $this->billing_date ? $this->billing_date?->format('Y-m-d') : '',
            'end_date' => $this->end_date ? $this->end_date?->format('Y-m-d') : '',
            'next_date_at' => $this->status === BillStatusEnum::COMPLETED
                ? null
                : $this->next_date_at?->format('Y-m-d'),
            'status' => $this->status,

            'category' => $this->category?->value,
            'frequency' => $this->frequency,
            'is_autopay' => $this->is_autopay,
            'description' => $this->description,
            'default_payment' => $this->default_payment?->value ?? '',
        ];
    }
}
