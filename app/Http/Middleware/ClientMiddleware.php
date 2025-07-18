<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('client')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('client.login');
        }

        $userId = Auth::guard('client')->id();
        $user = User::find($userId);

        if (!$user || $user->role_id !== 3) {
            abort(403, 'Unauthorized. Client access required.');
        }

        // Share auth user with Inertia
        if (class_exists('\Inertia\Inertia')) {
            $user->load(['client']);
            \Inertia\Inertia::share('auth', [
                'user' => $user,
                'role' => 'client'
            ]);
        }

        return $next($request);
    }
}