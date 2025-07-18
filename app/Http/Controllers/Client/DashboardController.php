<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display client dashboard statistics.
     */
    public function index()
    {
        $user = Auth::guard('client')->user();
        $client = $user->client;
        
        if (!$client) {
            abort(404, 'Client profile not found.');
        }
        
        $stats = [
            'total_tickets' => Ticket::where('client_id', $client->id)->count(),
            'open_tickets' => Ticket::where('client_id', $client->id)
                ->where('status', 'Open')->count(),
            'in_progress_tickets' => Ticket::where('client_id', $client->id)
                ->where('status', 'In Progress')->count(),
            'resolved_tickets' => Ticket::where('client_id', $client->id)
                ->where('status', 'Resolved')->count(),
            'closed_tickets' => Ticket::where('client_id', $client->id)
                ->where('status', 'Closed')->count(),
        ];

        $recentTickets = Ticket::where('client_id', $client->id)
            ->with(['agent.user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // Get ticket trend for last 7 days
        $ticketTrend = Ticket::where('client_id', $client->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return Inertia::render('Client/Dashboard', [
            'stats' => $stats,
            'recentTickets' => $recentTickets,
            'ticketTrend' => $ticketTrend,
        ]);
    }

    /**
     * Get client profile.
     */
    public function profile()
    {
        $userId = Auth::guard('client')->id();
        $user = User::with(['client', 'role'])->find($userId);

        return Inertia::render('Client/Profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update client profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'company' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string',
        ]);

        $userId = Auth::guard('client')->id();
        $user = User::find($userId);
        $client = $user->client;

        if (!$client) {
            abort(404, 'Client profile not found.');
        }

        // Update user name if provided
        if ($request->has('name')) {
            User::where('id', $userId)->update(['name' => $request->name]);
        }

        // Update client profile
        $client->update($request->only(['company', 'phone', 'address']));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}