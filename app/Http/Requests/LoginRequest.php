<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Override;

class LoginRequest extends BaseFormRequest
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
            'email' => ['required', 'exists:users,email'],
            'password' => ['required', 'string', 'max:18', 'regex:/^[A-Za-z0-9]+$/'],
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'email.exists' => 'Email does not exist.',
            'password.max' => 'Maximum password allowed is 18',
            'password.regex' => 'Numeric and Letters are allowed.',
        ];
    }
}
