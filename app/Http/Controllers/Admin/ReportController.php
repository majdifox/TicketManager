<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Agent;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Generate monthly report.
     */
    public function monthly(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = $request->month;
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $data = [
            'period' => $month,
            'ticket_stats' => $this->getTicketStats($startDate, $endDate),
            'category_breakdown' => $this->getCategoryBreakdown($startDate, $endDate),
            'priority_breakdown' => $this->getPriorityBreakdown($startDate, $endDate),
            'agent_performance' => $this->getAgentPerformance($startDate, $endDate),
            'resolution_times' => $this->getResolutionTimes($startDate, $endDate),
            'daily_tickets' => $this->getDailyTickets($startDate, $endDate),
        ];

        return response()->json($data);
    }

    /**
     * Export tickets to Excel.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|string',
            'agent_id' => 'nullable|exists:agents,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        return Excel::download(
            new TicketsExport(
                $request->start_date,
                $request->end_date,
                $request->status,
                $request->agent_id,
                $request->category_id
            ),
            'tickets-report-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export tickets to PDF.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tickets = Ticket::with(['client.user', 'agent.user', 'category'])
            ->whereBetween('created_at', [$request->start_date, $request->end_date])
            ->get();

        $data = [
            'tickets' => $tickets,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('reports.tickets-pdf', $data);
        
        return $pdf->download('tickets-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Get agent performance report.
     */
    public function agentPerformance(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $agents = Agent::with('user')
            ->withCount([
                'tickets' => function ($query) use ($request) {
                    $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                },
                'tickets as resolved_tickets' => function ($query) use ($request) {
                    $query->where('status', 'Resolved')
                        ->whereBetween('resolved_at', [$request->start_date, $request->end_date]);
                },
            ])
            ->get()
            ->map(function ($agent) use ($request) {
                $avgResolutionTime = Ticket::where('agent_id', $agent->id)
                    ->whereNotNull('resolved_at')
                    ->whereBetween('resolved_at', [$request->start_date, $request->end_date])
                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                    ->first();

                return [
                    'id' => $agent->id,
                    'name' => $agent->user->name,
                    'department' => $agent->department,
                    'total_tickets' => $agent->tickets_count,
                    'resolved_tickets' => $agent->resolved_tickets_count,
                    'resolution_rate' => $agent->tickets_count > 0 
                        ? round(($agent->resolved_tickets_count / $agent->tickets_count) * 100, 2) 
                        : 0,
                    'avg_resolution_time' => round($avgResolutionTime->avg_hours ?? 0, 1),
                ];
            });

        return response()->json($agents);
    }

    /**
     * Get ticket statistics for a period.
     */
    private function getTicketStats($startDate, $endDate)
    {
        return [
            'total' => Ticket::whereBetween('created_at', [$startDate, $endDate])->count(),
            'open' => Ticket::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'Open')->count(),
            'in_progress' => Ticket::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'In Progress')->count(),
            'resolved' => Ticket::whereBetween('resolved_at', [$startDate, $endDate])
                ->where('status', 'Resolved')->count(),
            'closed' => Ticket::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'Closed')->count(),
        ];
    }

    /**
     * Get category breakdown for a period.
     */
    private function getCategoryBreakdown($startDate, $endDate)
    {
        return Category::withCount(['tickets' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->get()
        ->map(function ($category) {
            return [
                'name' => $category->name,
                'count' => $category->tickets_count,
            ];
        });
    }

    /**
     * Get priority breakdown for a period.
     */
    private function getPriorityBreakdown($startDate, $endDate)
    {
        return Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');
    }

    /**
     * Get resolution times for a period.
     */
    private function getResolutionTimes($startDate, $endDate)
    {
        $times = Ticket::whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$startDate, $endDate])
            ->selectRaw('
                AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours,
                MIN(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as min_hours,
                MAX(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as max_hours
            ')
            ->first();

        return [
            'average' => round($times->avg_hours ?? 0, 1),
            'minimum' => round($times->min_hours ?? 0, 1),
            'maximum' => round($times->max_hours ?? 0, 1),
        ];
    }

    /**
     * Get daily ticket counts for a period.
     */
    private function getDailyTickets($startDate, $endDate)
    {
        return Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');
    }
}