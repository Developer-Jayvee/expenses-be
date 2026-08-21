<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('auth-token');
        if (!$token) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (! $accessToken) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
        Auth::setUser($accessToken->tokenable);
        return $next($request);
    }
}
