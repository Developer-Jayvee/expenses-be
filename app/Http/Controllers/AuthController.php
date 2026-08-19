<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class AuthController extends Controller
{
    protected AuthService $_authService;

    public function __construct(AuthService $authService)
    {
        $this->_authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $password = $request->validated('password');

        return $this->_authService->login(email : $email, password : $password);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->_authService->register(
            firstName: $request->validated('first_name'),
            lastName: $request->validated('last_name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            invitationCode: $request->validated('invitation_code'),
        );
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                throw new UnauthorizedException('Unauthorized');
            }
            $user->currentAccessToken()?->delete();

            return $this->successMessage('Logout successfully')
                ->withoutCookie('auth-token');
        } catch (Exception $e) {
            return $this->errorResponse($e, $e->getCode());
        }

    }
}
