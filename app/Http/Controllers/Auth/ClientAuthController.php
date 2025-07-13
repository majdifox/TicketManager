<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Client;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class ClientAuthController extends Controller
{
    /**
     * Display the client login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ClientLogin');
    }

    /**
     * Handle an incoming client authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        
        // Check if user exists and is client
        $user = User::where('email', $credentials['email'])
            ->where('role_id', 3)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'These credentials do not match our client records.',
            ]);
        }

        if (Auth::guard('client')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/client/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Display the client registration view.
     */
    public function showRegistrationForm(): Response
    {
        return Inertia::render('Auth/ClientRegister');
    }

    /**
     * Handle an incoming client registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3, // Client role
        ]);

        Client::create([
            'user_id' => $user->id,
            'company' => $request->company,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        event(new Registered($user));

        Auth::guard('client')->login($user);

        return redirect('/client/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/client/login');
    }
}