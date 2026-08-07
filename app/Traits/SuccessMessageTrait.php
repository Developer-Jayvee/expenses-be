<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait SuccessMessageTrait
{
    use UtilitiesTrait;

    /**
     * Success Response
     *
     * @param  mixed
     */
    public function successMessage(string $message = 'Success', mixed $data = null, int $code = 200): JsonResponse
    {
        return response()->json($this->setReturnResponse($data, $message), $code);
    }
}
