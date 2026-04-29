<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Force JSON response for this request
        $request->headers->set('Accept', 'application/json');

        $key = $request->input('key') ?? $request->bearerToken();

        if (!$key) {
            return response()->json(['error' => 'API key is required.'], 401);
        }

        $user = User::where('api_token_key', $key)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid API key.'], 401);
        }

        if ($user->is_blocked) {
            return response()->json(['error' => 'Your account has been suspended.'], 403);
        }

        // Bind user to the request without touching the web session
        auth()->guard('web')->setUser($user);

        return $next($request);
    }
}
