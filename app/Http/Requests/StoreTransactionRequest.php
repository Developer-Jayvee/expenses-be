<?php

namespace App\Http\Requests;

use App\Enums\PaymentTypesEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class StoreTransactionRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'billsId' => ['required', 'exists:bills,id'],
            'transaction_date' => ['required', 'date'],
            'payment_mode' => ['required', new Enum(PaymentTypesEnum::class)],
            'notes' => ['nullable', 'string'],
            'amount' => ['required', 'integer'],
            'periods' => ['nullable', 'integer', 'min:1', 'max:60'],
        ];
    }
}
