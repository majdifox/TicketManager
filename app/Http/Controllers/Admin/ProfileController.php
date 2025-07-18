<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Agent;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the admin profile.
     */
    public function show()
    {
        $userId = Auth::guard('admin')->id();
        $user = User::with(['admin', 'role'])->find($userId);

        if (!$user || !$user->admin) {
            abort(404, 'Admin profile not found.');
        }

        // Get system statistics for the admin dashboard overview
        $stats = [
            // Ticket statistics
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::where('status', 'Open')->count(),
            'in_progress_tickets' => Ticket::where('status', 'In Progress')->count(),
            'resolved_tickets' => Ticket::where('status', 'Resolved')->count(),
            'closed_tickets' => Ticket::where('status', 'Closed')->count(),
            
            // User statistics
            'total_agents' => Agent::count(),
            'available_agents' => Agent::where('is_available', true)->count(),
            'total_clients' => User::where('role_id', 3)->count(),
            'total_categories' => Category::where('is_active', true)->count(),
            
            // Performance metrics
            'avg_resolution_time' => $this->getAverageResolutionTime(),
        ];

        return Inertia::render('Admin/Profile', [
            'user' => $user,
            'stats' => $stats,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Update the admin profile.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'department' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);

        $userId = Auth::guard('admin')->id();
        $user = User::find($userId);
        $admin = $user->admin;

        if (!$admin) {
            abort(404, 'Admin profile not found.');
        }

        // Update user name if provided
        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        }

        // Update admin profile
        $admin->update($request->only(['department', 'phone']));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Get average ticket resolution time in hours.
     */
    private function getAverageResolutionTime()
    {
        $avgTime = DB::table('tickets')
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->first();

        return round($avgTime->avg_hours ?? 0, 1);
    }
}