<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait SuccessMessageTrait
{    
    use UtilitiesTrait;
    /**
     * Success Response
     *
     * @param  string $message
     * @param  array | string | int | null $data
     * @param  int $code
     * @return JsonResponse
     */
    public function successMessage(string $message = 'Success' , array | string | int | null $data = null , int $code = 200) : JsonResponse
    {
        return response()->json($this->setReturnResponse($data,$message),$code);
    }
}
