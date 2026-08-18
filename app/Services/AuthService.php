<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ErrorMessageTrait;
use App\Traits\SuccessMessageTrait;
use App\Traits\UtilitiesTrait;
use Illuminate\Http\JsonResponse;

class AuthService
{
    use ErrorMessageTrait, SuccessMessageTrait , UtilitiesTrait;

    public function login(string $email, string $password): JsonResponse
    {
        try {
            $user = User::where('email', $email)->first();

            $token = $user->createToken('user-auth')->plainTextToken;

            return response()->json(
                $this->setReturnResponse(['user' => $user], 'Successfully Login')
            )->withCookie(cookie('auth-token', $token, 60, '/', null, false, true));
        } catch (\Throwable $th) {
            return $this->errorResponse($th, $th->getCode());
        }
    }

    public function register(string $firstName, string $lastName, string $email, string $password): JsonResponse
    {
        try {
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $password,
            ]);

            return response()->json(
                $this->setReturnResponse(['user' => $user], 'Account created successfully'),
                201
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th, $th->getCode());
        }
    }
}
