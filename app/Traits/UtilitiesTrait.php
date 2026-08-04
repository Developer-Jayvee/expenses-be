<?php

namespace App\Traits;

trait UtilitiesTrait
{    
    /**
     * Format response 
     *
     * @param  mixed $data
     * @param  mixed $message
     * @return array
     */
    public function setReturnResponse(mixed $data , string $message): array
    {
        return [
            'data' => $data,
            'message' => $message
        ];
    }
}
