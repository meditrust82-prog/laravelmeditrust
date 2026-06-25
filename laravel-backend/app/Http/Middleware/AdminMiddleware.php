<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user() ?: $request->attributes->get('user');
        if (!$user || (($user->role ?? null) !== 'admin' && empty($user->is_admin))) {
            return response()->json(['message'=>'Forbidden'],403);
        }
        return $next($request);
    }
}
