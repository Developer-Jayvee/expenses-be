<?php

namespace App\Http\Requests;

use App\Enums\BillCategoryEnum;
use App\Enums\BillStatusEnum;
use App\Enums\PaymentTypesEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class StoreBillsRequest extends BaseFormRequest
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
            'name' => ['required'],
            'amount' => ['required', 'numeric', 'min_digits:1'],
            'billing_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'status' => ['required', new Enum(BillStatusEnum::class)],

            'category' => ['required', new Enum(BillCategoryEnum::class)],
            'is_autopay' => ['boolean', 'sometimes'],
            'description' => ['nullable', 'string'],
            'frequency' => ['required', 'in:monthly,yearly,daily,once'],
            'default_payment' => ['sometimes', new Enum(PaymentTypesEnum::class)],
        ];
    }
}
