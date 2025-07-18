<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Agent;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log; 

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
   public function index(Request $request)
{
    $startDate = $request->get('start', now()->subMonth()->format('Y-m-d'));
    $endDate = $request->get('end', now()->format('Y-m-d'));

    // Get ticket statistics
    $totalTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->count();
    $resolvedTickets = Ticket::where('status', 'Resolved')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    $pendingTickets = Ticket::whereIn('status', ['Open', 'In Progress'])
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // Get tickets by status
    $byStatus = Ticket::whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

    // Get tickets by priority
    $byPriority = Ticket::whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('priority, COUNT(*) as count')
        ->groupBy('priority')
        ->pluck('count', 'priority')
        ->toArray();

    // Get top categories
    $topCategories = Category::withCount(['tickets' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->orderBy('tickets_count', 'desc')
        ->take(5)
        ->get()
        ->map(function ($category) {
            return [
                'name' => $category->name,
                'count' => $category->tickets_count,
            ];
        })->toArray();

    // Get agent performance
    $agentPerformance = Agent::with('user')
        ->withCount(['tickets' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'Resolved')
                  ->whereBetween('resolved_at', [$startDate, $endDate]);
        }])
        ->orderBy('tickets_count', 'desc')
        ->take(5)
        ->get()
        ->map(function ($agent) {
            return [
                'name' => $agent->user->name,
                'resolved' => $agent->tickets_count,
            ];
        })->toArray();

    // Calculate average resolution time
    $avgResolutionTime = Ticket::whereNotNull('resolved_at')
        ->whereBetween('resolved_at', [$startDate, $endDate])
        ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
        ->first();
    
    $avgTime = round($avgResolutionTime->avg_hours ?? 0, 1) . 'h';

    $stats = [
        'total_tickets' => $totalTickets,
        'resolved_tickets' => $resolvedTickets,
        'pending_tickets' => $pendingTickets,
        'avg_resolution_time' => $avgTime,
        'by_status' => $byStatus,
        'by_priority' => $byPriority,
        'top_categories' => $topCategories,
        'agent_performance' => $agentPerformance,
    ];

    return Inertia::render('Admin/Reports/Index', [
        'stats' => $stats,
    ]);
}

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
     * Get average resolution time.
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

    /**
     * Export tickets report.
     */
   public function export(Request $request)
{
    try {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'format' => 'required|in:csv,pdf',
            'status' => 'nullable|string',
            'agent_id' => 'nullable|exists:agents,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Get tickets data
        $tickets = Ticket::with(['client.user', 'agent.user', 'category'])
            ->whereBetween('created_at', [$request->start, $request->end])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->agent_id, function ($query, $agent) {
                return $query->where('agent_id', $agent);
            })
            ->when($request->category_id, function ($query, $category) {
                return $query->where('category_id', $category);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get stats for PDF
        $stats = [
            'total_tickets' => $tickets->count(),
            'resolved_tickets' => $tickets->where('status', 'Resolved')->count(),
            'pending_tickets' => $tickets->whereIn('status', ['Open', 'In Progress'])->count(),
            'period' => $request->start . ' to ' . $request->end,
        ];

        if ($request->format === 'csv') {
            return $this->exportCsv($tickets);
        } else {
            return $this->exportPdf($tickets, $stats);
        }
    } catch (\Exception $e) {
        Log::error('Export Error: ' . $e->getMessage());
        Log::error('Export Error Stack: ' . $e->getTraceAsString());
        
        return response()->json([
            'error' => 'Export failed: ' . $e->getMessage(),
            'debug' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
}

    /**
     * Export as CSV
     */
    private function exportCsv($tickets)
    {
        $filename = 'tickets-report-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Ticket Number', 'Subject', 'Client', 'Agent', 'Category', 'Status', 'Priority', 'Created At', 'Resolved At']);
            
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->id,
                    $ticket->ticket_number,
                    $ticket->subject,
                    $ticket->client->user->name ?? 'N/A',
                    $ticket->agent->user->name ?? 'Unassigned',
                    $ticket->category->name ?? 'N/A',
                    $ticket->status,
                    $ticket->priority,
                    $ticket->created_at->format('Y-m-d H:i:s'),
                    $ticket->resolved_at ? $ticket->resolved_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export as PDF
     */
   private function exportPdf($tickets, $stats)
{
    try {
        Log::info('Starting PDF export with ' . $tickets->count() . ' tickets');
        Log::info('PDF Stats: ' . json_encode($stats));
        
        // Generate PDF using the same method as debug route
        $pdf = Pdf::loadView('reports.tickets-pdf', [
            'tickets' => $tickets,
            'stats' => $stats
        ]);

        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'DejaVu Sans',
        ]);

        $filename = 'tickets-report-' . date('Y-m-d-H-i-s') . '.pdf';
        
        // Generate PDF content
        $pdfContent = $pdf->output();
        Log::info('PDF content generated. Size: ' . strlen($pdfContent) . ' bytes');
        
        // Check if content was generated
        if (empty($pdfContent)) {
            throw new \Exception('PDF content is empty after generation');
        }
        
        // Return as download using the same method as debug route
        return response()->streamDownload(function() use ($pdfContent) {
            echo $pdfContent;
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($pdfContent),
        ]);
        
    } catch (\Exception $e) {
        Log::error('PDF Generation Error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'error' => 'Failed to generate PDF: ' . $e->getMessage(),
            'tickets_count' => $tickets->count(),
            'debug_info' => [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]
        ], 500);
    }
}

    /**
     * Get agent performance for a period.
     */
    private function getAgentPerformance($startDate, $endDate)
    {
        return Agent::with('user')
            ->withCount(['tickets' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'Resolved')
                      ->whereBetween('resolved_at', [$startDate, $endDate]);
            }])
            ->orderBy('tickets_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($agent) {
                return [
                    'name' => $agent->user->name,
                    'resolved' => $agent->tickets_count,
                ];
            });
    }
}