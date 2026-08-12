<?php

namespace App\Traits;

trait UtilitiesTrait
{    
    /**
     * Format response 
     *
     * @param  mixed $data
     * @param  string $message
     * @param bool $status
     * @return array
     */
    public function setReturnResponse(mixed $data , string $message , bool $status = true): array
    {
        return [
            'data' => $data,
            'message' => $message,
            'status' => $status
        ];
    }
}
