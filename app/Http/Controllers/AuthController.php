<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Handle client registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        $user = DB::transaction(function () use ($request) {
            // Create user with client role
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => Role::CLIENT,
            ]);

            // Create client profile
            Client::create([
                'user_id' => $user->id,
                'company' => $request->company,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return $user;
        });

        Auth::login($user);

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user->load('role', 'client'),
            'token' => $user->createToken('auth-token')->plainTextToken,
            'redirect' => $user->getRedirectPath(),
        ], 201);
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $user->load('role');

        // Load role-specific profile
        if ($user->isAdmin()) {
            $user->load('admin');
        } elseif ($user->isAgent()) {
            $user->load('agent');
        } elseif ($user->isClient()) {
            $user->load('client');
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $user->createToken('auth-token')->plainTextToken,
            'redirect' => $user->getRedirectPath(),
        ]);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $user->load('role');

        // Load role-specific profile
        if ($user->isAdmin()) {
            $user->load('admin');
        } elseif ($user->isAgent()) {
            $user->load('agent');
        } elseif ($user->isClient()) {
            $user->load('client');
        }

        return response()->json($user);
    }
}