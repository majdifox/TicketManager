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
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with(['role'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role_id, function ($query, $role) {
                $query->where('role_id', $role);
            });

        $users = $query->paginate(15);

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

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role_id']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::whereIn('id', [1, 2])->get(); // Only Admin and Agent

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
        ]);
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

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
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

        return Inertia::render('Admin/Users/Show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $user->load('role');
        
        if ($user->isAdmin()) {
            $user->load('admin');
        } elseif ($user->isAgent()) {
            $user->load('agent');
        } elseif ($user->isClient()) {
            $user->load('client');
        }

        $roles = Role::whereIn('id', [1, 2])->get();

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Prevent updating client to admin/agent role
        if ($user->isClient() && in_array($request->role_id, [1, 2])) {
            return redirect()->back()
                ->withErrors(['role_id' => 'Cannot change client role to admin or agent.']);
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

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if (auth()->id() === $user->id) {
            return redirect()->back()
                ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}