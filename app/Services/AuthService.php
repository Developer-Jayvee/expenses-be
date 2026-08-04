<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ErrorMessageTrait;
use App\Traits\SuccessMessageTrait;
use App\Traits\UtilitiesTrait;
use Illuminate\Http\JsonResponse;

class AuthService
{
    use SuccessMessageTrait, ErrorMessageTrait , UtilitiesTrait;
    public function login(string $email , string $password) : JsonResponse
    {
        try {
            $user = User::where('email',$email)->first();

            $token = $user->createToken('user-auth')->plainTextToken;
            
            return response()->json(
                $this->setReturnResponse(['user' => $user],"Successfully Login")
            )->withCookie(cookie('auth-token',$token,60,'/',null,false,true));
        } catch (\Throwable $th) {
            return $this->errorResponse($th,$th->getCode());
        }
    }
}
