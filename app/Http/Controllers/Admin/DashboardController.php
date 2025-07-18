<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Agent;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

        $recentTickets = Ticket::with(['client.user', 'agent.user', 'category'])
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentTickets' => $recentTickets,
        ]);
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

    /**
     * Get ticket count by priority - Fixed format for PriorityChart component.
     */
    private function getTicketsByPriority()
    {
        $priorityData = DB::table('tickets')
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get();

        // Transform to format expected by PriorityChart component
        return $priorityData->map(function ($item) {
            return [
                'priority' => $item->priority,
                'count' => $item->count
            ];
        })->toArray();
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
     * Get agent performance metrics - Fixed format for AgentPerformanceTable component.
     */
    private function getAgentPerformance()
    {
        return Agent::with('user:id,name,email')
            ->withCount([
                'tickets as total_tickets',
                'tickets as open_tickets' => function ($query) {
                    $query->whereIn('status', ['Open', 'In Progress']);
                },
                'tickets as resolved_tickets' => function ($query) {
                    $query->where('status', 'Resolved');
                },
                'tickets as resolved_today' => function ($query) {
                    $query->where('status', 'Resolved')
                          ->whereDate('updated_at', today());
                },
            ])
            ->get()
            ->map(function ($agent) {
                // Calculate performance score based on resolved vs total tickets
                $performanceScore = 0;
                if ($agent->total_tickets > 0) {
                    $performanceScore = round(($agent->resolved_tickets / $agent->total_tickets) * 100);
                }

                return [
                    'id' => $agent->id,
                    'name' => $agent->user->name,
                    'email' => $agent->user->email ?? 'No email',
                    'total_tickets' => $agent->total_tickets,
                    'open_tickets' => $agent->open_tickets,
                    'resolved_tickets' => $agent->resolved_tickets,
                    'resolved_today' => $agent->resolved_today,
                    'is_available' => $agent->is_available,
                    'performance_score' => $performanceScore,
                ];
            });
    }
}