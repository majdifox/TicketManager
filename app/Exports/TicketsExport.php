<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketsExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $status;
    protected $agentId;
    protected $categoryId;

    public function __construct($startDate, $endDate, $status = null, $agentId = null, $categoryId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->agentId = $agentId;
        $this->categoryId = $categoryId;
    }

    /**
     * Build the query for export.
     */
    public function query()
    {
        return Ticket::with(['client.user', 'agent.user', 'category'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->when($this->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($this->agentId, function ($query, $agentId) {
                return $query->where('agent_id', $agentId);
            })
            ->when($this->categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->orderBy('created_at', 'desc');
    }

    /**
     * Define the headings for the export.
     */
    public function headings(): array
    {
        return [
            'Ticket Number',
            'Subject',
            'Description',
            'Category',
            'Priority',
            'Status',
            'Client Name',
            'Client Email',
            'Client Company',
            'Assigned Agent',
            'Created Date',
            'Resolved Date',
            'Resolution Time (Hours)',
        ];
    }

    /**
     * Map the data for each row.
     */
    public function map($ticket): array
    {
        $resolutionTime = null;
        if ($ticket->resolved_at) {
            $resolutionTime = $ticket->created_at->diffInHours($ticket->resolved_at);
        }

        return [
            $ticket->ticket_number,
            $ticket->subject,
            $ticket->description,
            $ticket->category->name,
            $ticket->priority,
            $ticket->status,
            $ticket->client->user->name,
            $ticket->client->user->email,
            $ticket->client->company ?? 'N/A',
            $ticket->agent ? $ticket->agent->user->name : 'Unassigned',
            $ticket->created_at->format('Y-m-d H:i:s'),
            $ticket->resolved_at ? $ticket->resolved_at->format('Y-m-d H:i:s') : 'N/A',
            $resolutionTime ?? 'N/A',
        ];
    }

    /**
     * Define column widths.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Ticket Number
            'B' => 30,  // Subject
            'C' => 50,  // Description
            'D' => 20,  // Category
            'E' => 10,  // Priority
            'F' => 15,  // Status
            'G' => 20,  // Client Name
            'H' => 25,  // Client Email
            'I' => 20,  // Client Company
            'J' => 20,  // Assigned Agent
            'K' => 20,  // Created Date
            'L' => 20,  // Resolved Date
            'M' => 20,  // Resolution Time
        ];
    }

    /**
     * Apply styles to the worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => ['font' => ['bold' => true]],
        ];
    }
}