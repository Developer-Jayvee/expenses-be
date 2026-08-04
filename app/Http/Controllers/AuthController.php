<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        
        return $this->_authService->login(email : $email ,password : $password );
    }
}
