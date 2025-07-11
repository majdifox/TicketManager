<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAgent()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Agent access required.'], 403);
            }
            
            abort(403, 'Unauthorized. Agent access required.');
        }

        return $next($request);
    }
}