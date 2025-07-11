<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $users = User::with(['role'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role_id, function ($query, $role) {
                $query->where('role_id', $role);
            })
            ->paginate(15);

        // Load role-specific profiles
        $users->getCollection()->transform(function ($user) {
            if ($user->isAdmin()) {
                $user->load('admin');
            } elseif ($user->isAgent()) {
                $user->load('agent');
            } elseif ($user->isClient()) {
                $user->load('client');
            }
            return $user;
        });

        return response()->json($users);
    }

    /**
     * Store a newly created user (Admin or Agent only).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|in:1,2', // Only Admin and Agent
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            if ($request->role_id == 1) {
                Admin::create([
                    'user_id' => $user->id,
                    'department' => $request->department,
                    'phone' => $request->phone,
                ]);
            } else {
                Agent::create([
                    'user_id' => $user->id,
                    'department' => $request->department,
                    'phone' => $request->phone,
                    'is_available' => true,
                ]);
            }

            return $user;
        });

        $user->load('role');
        
        if ($user->isAdmin()) {
            $user->load('admin');
        } else {
            $user->load('agent');
        }

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user,
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('role');
        
        if ($user->isAdmin()) {
            $user->load('admin');
        } elseif ($user->isAgent()) {
            $user->load(['agent.categories', 'agent.tickets' => function ($query) {
                $query->latest()->take(5);
            }]);
        } elseif ($user->isClient()) {
            $user->load(['client', 'client.tickets' => function ($query) {
                $query->latest()->take(5);
            }]);
        }

        return response()->json($user);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Prevent updating client to admin/agent role
        if ($user->isClient() && in_array($request->role_id, [1, 2])) {
            return response()->json([
                'message' => 'Cannot change client role to admin or agent.',
            ], 422);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_available' => 'sometimes|boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Update role-specific profile
        $profile = $user->getRoleSpecificProfile();
        if ($profile) {
            $updateData = $request->only(['department', 'phone']);
            
            // Handle agent-specific fields
            if ($user->isAgent() && $request->has('is_available')) {
                $updateData['is_available'] = $request->is_available;
            }
            
            $profile->update($updateData);
        }

        $user->load('role');
        
        if ($user->isAdmin()) {
            $user->load('admin');
        } elseif ($user->isAgent()) {
            $user->load('agent');
        } elseif ($user->isClient()) {
            $user->load('client');
        }

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user,
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Get all roles.
     */
    public function roles()
    {
        $roles = Role::all();

        return response()->json($roles);
    }
}