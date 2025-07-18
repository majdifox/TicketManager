<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MultiGuardMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via any guard
        $guards = ['admin', 'agent', 'client'];
        $authenticatedUser = null;
        $authenticatedGuard = null;
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $authenticatedUser = Auth::guard($guard)->user();
                $authenticatedGuard = $guard;
                break;
            }
        }
        
        if ($authenticatedUser) {
            // Set the authenticated user for the request
            $request->setUserResolver(function () use ($authenticatedUser) {
                return $authenticatedUser;
            });
            
            // Set the default auth guard to the authenticated guard
            Auth::shouldUse($authenticatedGuard);
            
            // For Chatify routes, ensure we have a proper auth user set
            if ($request->is('chatify*') || $request->is('tickets/*/chat*')) {
                // Override auth()->user() for Chatify
                app()->singleton('auth.user', function () use ($authenticatedUser) {
                    return $authenticatedUser;
                });
                
                // Set session auth
                session(['auth.user' => $authenticatedUser]);
            }
            
            // Ensure the user has required fields for Chatify
            $this->ensureChatifyCompatibility($authenticatedUser);
            
            return $next($request);
        }
        
        // If no guard is authenticated, return unauthorized response
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        return redirect()->route('client.login');
    }
    
    /**
     * Ensure user has required fields for Chatify compatibility.
     */
    private function ensureChatifyCompatibility($user)
    {
        // If messenger_color is null, set a default value
        if (is_null($user->messenger_color)) {
            $user->messenger_color = '#2180f3'; // Default blue color
            $user->save();
        }
        
        // If active_status is null, set a default value
        if (is_null($user->active_status)) {
            $user->active_status = 1;
            $user->save();
        }
    }
}