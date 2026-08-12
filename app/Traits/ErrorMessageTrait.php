<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

trait ErrorMessageTrait
{    
    use LoggerTrait, UtilitiesTrait;
    /**
     * Error response
     *
     * @param  Throwable | Exception | null $error
     * @param  int $code
     * @return JsonResponse
     */
    public function errorResponse(Throwable | Exception | null $error = null , int $code = 500) : JsonResponse
    {
        if($error->getMessage()){
            $this->logEntry($error->getMessage(),['code' => $error->getCode() ?? $code , 'error' => $error->getTrace()]);
            return response()->json(
                $this->setReturnResponse([
                    'trace' => $error->getTrace()
                ],$error->getMessage(),false)
            );
        }

        return response()->json("Error found. Plase contact your admin for further assistance.",$code);
    }    
    /**
     * Logs
     *
     * @param  string $message
     * @param   array | null $context
     * @return void
     */
    protected function logEntry(string $message , ?array $context = null) : void
    {
        Log::alert($message,$context ?? []);
    }
}
