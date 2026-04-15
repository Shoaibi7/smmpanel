<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /* \Log::info('AdminMiddleware check', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->role ?? 'none',
            'is_auth' => auth()->check()
        ]); */

        if (!auth()->check() || strtolower(auth()->user()->role) !== 'admin') {
            \Log::warning('Admin access denied', [
                'user_id' => auth()->id(),
                'role' => auth()->user()?->role ?? 'none'
            ]);
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
