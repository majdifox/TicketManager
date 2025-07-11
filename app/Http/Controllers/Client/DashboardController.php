<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display client dashboard statistics.
     */
    public function index()
    {
        $client = Auth::user()->client;
        
        if (!$client) {
            return response()->json(['message' => 'Client profile not found.'], 404);
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

        $recent_tickets = Ticket::where('client_id', $client->id)
            ->with(['agent.user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // Get ticket trend for last 7 days
        $ticket_trend = Ticket::where('client_id', $client->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_tickets' => $recent_tickets,
            'ticket_trend' => $ticket_trend,
        ]);
    }

    /**
     * Get client's tickets with filters.
     */
    public function tickets(Request $request)
    {
        $client = Auth::user()->client;
        
        if (!$client) {
            return response()->json(['message' => 'Client profile not found.'], 404);
        }

        $query = Ticket::where('client_id', $client->id)
            ->with(['agent.user', 'category']);

        // Apply filters
        $query->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        })
        ->when($request->priority, function ($q, $priority) {
            return $q->where('priority', $priority);
        })
        ->when($request->category_id, function ($q, $category) {
            return $q->where('category_id', $category);
        });

        $tickets = $query->latest()->paginate(15);

        return response()->json($tickets);
    }

    /**
     * Get active categories for ticket creation.
     */
    public function categories()
    {
        $categories = Category::active()
            ->select('id', 'name', 'description')
            ->get();

        return response()->json($categories);
    }

    /**
     * Get client profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load(['client', 'role']);

        return response()->json($user);
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

        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            return response()->json(['message' => 'Client profile not found.'], 404);
        }

        // Update user name if provided
        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        }

        // Update client profile
        $client->update($request->only(['company', 'phone', 'address']));

        $user->load(['client', 'role']);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ]);
    }
}