<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('agent')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('agent.login');
        }

        $userId = Auth::guard('agent')->id();
        $user = User::find($userId);

        if (!$user || $user->role_id !== 2) {
            abort(403, 'Unauthorized. Agent access required.');
        }

        // Share auth user with Inertia
        if (class_exists('\Inertia\Inertia')) {
            $user->load(['agent']);
            \Inertia\Inertia::share('auth', [
                'user' => $user,
                'role' => 'agent'
            ]);
        }

        return $next($request);
    }
}