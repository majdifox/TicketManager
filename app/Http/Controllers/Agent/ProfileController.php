<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the agent profile.
     */
    public function show()
    {
        $userId = Auth::guard('agent')->id();
        $user = User::with(['agent', 'role'])->find($userId);
        
        if (!$user || !$user->agent) {
            abort(404, 'Agent profile not found.');
        }

        $agent = $user->agent;

        // Get agent statistics
        $stats = [
            'total_tickets' => Ticket::where('agent_id', $agent->id)->count(),
            'open_tickets' => Ticket::where('agent_id', $agent->id)
                ->where('status', 'Open')->count(),
            'in_progress_tickets' => Ticket::where('agent_id', $agent->id)
                ->where('status', 'In Progress')->count(),
            'resolved_tickets' => Ticket::where('agent_id', $agent->id)
                ->where('status', 'Resolved')->count(),
            'pending_tickets' => Ticket::where('agent_id', $agent->id)
                ->whereIn('status', ['Open', 'In Progress'])->count(),
            'categories' => $agent->categories()->pluck('name'),
            'is_available' => $agent->is_available,
        ];

        return Inertia::render('Agent/Profile', [
            'user' => $user,
            'stats' => $stats,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Update the agent profile.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'department' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);

        $userId = Auth::guard('agent')->id();
        $user = User::find($userId);
        $agent = $user->agent;

        if (!$agent) {
            abort(404, 'Agent profile not found.');
        }

        // Update user name if provided
        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        }

        // Update agent profile
        $agent->update($request->only(['department', 'phone']));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}