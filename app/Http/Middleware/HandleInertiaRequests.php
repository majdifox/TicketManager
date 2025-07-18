<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\User;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Determine which guard is currently authenticated
        $guard = null;
        $user = null;
        $role = null;

        if (auth()->guard('admin')->check()) {
            $guard = 'admin';
            $userId = auth()->guard('admin')->id();
            $user = User::find($userId);
            $role = 'admin';
        } elseif (auth()->guard('agent')->check()) {
            $guard = 'agent';
            $userId = auth()->guard('agent')->id();
            $user = User::find($userId);
            $role = 'agent';
        } elseif (auth()->guard('client')->check()) {
            $guard = 'client';
            $userId = auth()->guard('client')->id();
            $user = User::find($userId);
            $role = 'client';
        }

        // Load role-specific relationships
        if ($user) {
            if ($role === 'admin') {
                $user->load('admin');
            } elseif ($role === 'agent') {
                $user->load('agent');
            } elseif ($role === 'client') {
                $user->load('client');
            }
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'role' => $role,
                'guard' => $guard,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}