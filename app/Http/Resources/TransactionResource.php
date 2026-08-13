<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bills_id' => $this->bills_id,
            'user_id' => $this?->user_id,
            'user' => $this->user,
            'payment_mode' => [
                'value' => $this->payment_mode->value,
                'label' => $this->payment_mode?->label()
            ],
            'amount' => $this->amount ? number_format($this->amount,2) : 0,
            'change' => $this->change ? number_format($this->change,2) : 0,
            'order' => $this->order,
            'notes' => $this->notes,
            'transaction_date' => $this->transaction_date?->format('Y-m-d')
        ];
    }
}
