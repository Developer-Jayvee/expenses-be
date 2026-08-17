<?php

namespace App\Http\Requests;

use App\Traits\ErrorMessageTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class BaseFormRequest extends FormRequest
{
    use ErrorMessageTrait;

    #[Override]
    protected function failedValidation(Validator $validator)
    {
        return $this->validationErrorResponse(
            'Validation Error',
            $validator
        );
    }
}
