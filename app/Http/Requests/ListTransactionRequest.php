<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class ListTransactionRequest extends BaseFormRequest
{
    public const SORTABLE_COLUMNS = ['transaction_date', 'amount', 'created_at', 'order'];

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
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'sort_by' => ['sometimes', 'string', Rule::in(self::SORTABLE_COLUMNS)],
            'sort_dir' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
