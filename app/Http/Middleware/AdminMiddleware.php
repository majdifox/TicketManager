<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

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

        $userId = Auth::guard('admin')->id();
        $user = User::find($userId);

        if (!$user || $user->role_id !== 1) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Share auth user with Inertia
        if (class_exists('\Inertia\Inertia')) {
            $user->load(['admin']);
            \Inertia\Inertia::share('auth', [
                'user' => $user,
                'role' => 'admin'
            ]);
        }

        return $next($request);
    }
}