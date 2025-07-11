<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isClient()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Client access required.'], 403);
            }
            
            abort(403, 'Unauthorized. Client access required.');
        }

        return $next($request);
    }
}