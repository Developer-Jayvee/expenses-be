<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
use Throwable;

trait ErrorMessageTrait
{
    use LoggerTrait, UtilitiesTrait;

    /**
     * Error response
     */
    public function errorResponse(Throwable|Exception|null $error = null, int $code = 500): JsonResponse
    {
        if ($error->getMessage()) {
            $this->logEntry($error->getMessage(), ['code' => $error->getCode() ?? $code, 'error' => $error->getTrace()]);

            return response()->json(
                $this->setReturnResponse([
                    'trace' => $error->getTrace(),
                ], $error->getMessage(), false)
            );
        }

        return response()->json('Error found. Plase contact your admin for further assistance.', $code);
    }

    public function validationErrorResponse(string $message, Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                $this->setReturnResponse(
                    $validator->errors(),
                    'Validation Failed',
                    false
                ), 422
            )
        );
    }

    /**
     * Logs
     */
    protected function logEntry(string $message, ?array $context = null): void
    {
        Log::alert($message, $context ?? []);
    }
}
