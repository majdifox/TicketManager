<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AgentAuthController extends Controller
{
    /**
     * Display the agent login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/AgentLogin');
    }

    /**
     * Handle an incoming agent authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        
        // Check if user exists and is agent
        $user = User::where('email', $credentials['email'])
            ->where('role_id', 2)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'These credentials do not match our agent records.',
            ]);
        }

        if (Auth::guard('agent')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/agent/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('agent')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/agent/login');
    }
}