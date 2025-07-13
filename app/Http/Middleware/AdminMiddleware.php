<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login');
        }

        if (Auth::guard('admin')->user()->role_id !== 1) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Share auth user with Inertia
        if (class_exists('\Inertia\Inertia')) {
            \Inertia\Inertia::share('auth', [
                'user' => Auth::guard('admin')->user()->load(['admin']),
                'role' => 'admin'
            ]);
        }

        return $next($request);
    }
}