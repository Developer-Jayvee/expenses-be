<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

trait ErrorMessageTrait
{
    use LoggerTrait, UtilitiesTrait;

    /**
     * Generic message shown to the client for errors that aren't safe to
     * describe in detail (unexpected/server-side failures).
     */
    private const GENERIC_ERROR_MESSAGE = 'Something went wrong. Please try again, or contact your admin if the problem continues.';

    /**
     * Error response
     */
    public function errorResponse(Throwable|Exception|null $error = null, int $code = 500): JsonResponse
    {
        if (! $error) {
            return response()->json(
                $this->setReturnResponse(null, self::GENERIC_ERROR_MESSAGE, false),
                $code
            );
        }

        $statusCode = $error instanceof HttpExceptionInterface
            ? $error->getStatusCode()
            : $code;

        if ($statusCode < 400 || $statusCode >= 600) {
            $statusCode = 500;
        }

        $this->logEntry($error->getMessage(), [
            'exception' => $error::class,
            'code' => $statusCode,
            'trace' => $error->getTraceAsString(),
        ], $statusCode >= 500 ? 'error' : 'warning');

        // 4xx = the user did something to cause this, so the exception's own
        // message is safe (and useful) to show. 5xx = unexpected/internal
        // failure, so only the generic message goes to the client — the
        // real detail already went to the Laravel log above.
        $message = $statusCode < 500 ? $error->getMessage() : self::GENERIC_ERROR_MESSAGE;

        return response()->json(
            $this->setReturnResponse(null, $message, false),
            $statusCode
        );
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
    protected function logEntry(string $message, ?array $context = null, string $level = 'error'): void
    {
        Log::log($level, $message, $context ?? []);
    }
}
