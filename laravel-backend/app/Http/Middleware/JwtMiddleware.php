<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JwtService;
use App\Models\User;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $jwt = new JwtService();
        try {
            $token = $request->cookie('access_token') ?: ($request->bearerToken() ?: null);
            if (!$token) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $userId = $jwt->getUserIdFromToken($token);
            if (!$userId) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $user = User::find($userId);
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            auth()->setUser($user);
            $request->attributes->set('user', $user);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return $next($request);
    }
}
