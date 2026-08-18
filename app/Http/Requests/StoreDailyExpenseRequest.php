<?php

namespace App\Http\Requests;

use App\Enums\DailyExpenseTypeEnum;
use App\Enums\PaymentTypesEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class StoreDailyExpenseRequest extends BaseFormRequest
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
            'name' => ['required', 'string'],
            'type' => ['required', new Enum(DailyExpenseTypeEnum::class)],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_type' => ['required', new Enum(PaymentTypesEnum::class)],
        ];
    }
}
