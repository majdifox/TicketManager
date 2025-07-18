<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display agent dashboard statistics.
     */
    public function index()
    {
        $userId = Auth::guard('agent')->id();
        $user = User::with('agent')->find($userId);
        $agent = $user->agent;
        
        if (!$agent) {
            abort(404, 'Agent profile not found.');
        }
        
        $stats = [
            'assigned_tickets' => Ticket::where('agent_id', $agent->id)->count(),
            'open_tickets' => Ticket::where('agent_id', $agent->id)
                ->where('status', 'Open')->count(),
            'in_progress_tickets' => Ticket::where('agent_id', $agent->id)
                ->where('status', 'In Progress')->count(),
            'resolved_tickets' => Ticket::where('agent_id', $agent->id)
                ->where('status', 'Resolved')->count(),
            'closed_tickets' => Ticket::where('agent_id', $agent->id)
                ->where('status', 'Closed')->count(),
            'categories' => $agent->categories()->pluck('name'),
            'is_available' => $agent->is_available,
        ];

        $recentTickets = Ticket::where('agent_id', $agent->id)
            ->with(['client.user', 'category'])
            ->latest()
            ->take(10)
            ->get();

        $priorityBreakdown = Ticket::where('agent_id', $agent->id)
            ->whereIn('status', ['Open', 'In Progress'])
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        return Inertia::render('Agent/Dashboard', [
            'stats' => $stats,
            'recentTickets' => $recentTickets,
            'priorityBreakdown' => $priorityBreakdown,
        ]);
    }

    /**
     * Update agent availability status.
     */
    public function updateAvailability(Request $request)
    {
        $request->validate([
            'is_available' => 'required|boolean',
        ]);

        $userId = Auth::guard('agent')->id();
        $user = User::with('agent')->find($userId);
        $agent = $user->agent;
        
        if (!$agent) {
            abort(404, 'Agent profile not found.');
        }

        $agent->update(['is_available' => $request->is_available]);

        return redirect()->back()->with('success', 'Availability status updated.');
    }
}