<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;

trait LoggerTrait
{
    public function logError(string $location,Exception $error , int $code = 500) 
    {
        Log::warning("$location Error",[
            'message' => $error->getMessage(),
            'trace' => $error->getTrace(),
            'code' => $error?->getCode() ?? $code
        ]);
    }
    public function logInfo(string $location,mixed $context) 
    {
        Log::info("$location Info",$context);
    }
}
