<?php

namespace App\Http\Requests;

use App\Enums\BillCategoryEnum;
use App\Enums\BillStatusEnum;
use App\Enums\PaymentTypesEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateBillsRequest extends FormRequest
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
            'name' => ['sometimes'],
            'amount' => ['sometimes','numeric'],
            'billing_date' => ['sometimes','date'],
            'end_date' => ['sometimes','date'],
            'status' => ['sometimes',new Enum(BillStatusEnum::class)],

            'category' => ['required',new Enum(BillCategoryEnum::class)],
            'is_autopay' => ['boolean','sometimes'],
            'description' => ['nullable','string'],
            'frequency' => ['required','in:monthly,yearly,daily,once'],
            'default_payment' => ['sometimes',new Enum(PaymentTypesEnum::class)]
        ];
    }
}
