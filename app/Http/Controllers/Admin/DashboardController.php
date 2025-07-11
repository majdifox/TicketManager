<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Agent;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard statistics.
     */
    public function index()
    {
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
            'tickets_by_priority' => $this->getTicketsByPriority(),
            'tickets_by_category' => $this->getTicketsByCategory(),
            'agent_performance' => $this->getAgentPerformance(),
        ];

        $recent_tickets = Ticket::with(['client.user', 'agent.user', 'category'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_tickets' => $recent_tickets,
        ]);
    }

    /**
     * Get average ticket resolution time in hours.
     */
    private function getAverageResolutionTime()
    {
        $avgTime = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->first();

        return round($avgTime->avg_hours ?? 0, 1);
    }

    /**
     * Get ticket count by priority.
     */
    private function getTicketsByPriority()
    {
        return Ticket::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');
    }

    /**
     * Get ticket count by category.
     */
    private function getTicketsByCategory()
    {
        return Ticket::selectRaw('category_id, COUNT(*) as count')
            ->with('category:id,name')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->name ?? 'Uncategorized',
                    'count' => $item->count,
                ];
            });
    }

    /**
     * Get agent performance metrics.
     */
    private function getAgentPerformance()
    {
        return Agent::with('user:id,name')
            ->withCount([
                'tickets as total_tickets',
                'tickets as open_tickets' => function ($query) {
                    $query->whereIn('status', ['Open', 'In Progress']);
                },
                'tickets as resolved_tickets' => function ($query) {
                    $query->where('status', 'Resolved');
                },
            ])
            ->get()
            ->map(function ($agent) {
                return [
                    'id' => $agent->id,
                    'name' => $agent->user->name,
                    'total_tickets' => $agent->total_tickets,
                    'open_tickets' => $agent->open_tickets,
                    'resolved_tickets' => $agent->resolved_tickets,
                    'is_available' => $agent->is_available,
                ];
            });
    }
}