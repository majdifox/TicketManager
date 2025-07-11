<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['client.user', 'agent.user', 'category']);

        // Filter based on user role
        if ($user->isClient() && $user->client) {
            $query->where('client_id', $user->client->id);
        } elseif ($user->isAgent() && $user->agent) {
            $query->where('agent_id', $user->agent->id);
        }

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

        return response()->json([
            'tickets' => $tickets,
            'statuses' => Ticket::getStatuses(),
            'priorities' => Ticket::getPriorities(),
        ]);
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only clients can create tickets
        if (!$user->isClient() || !$user->client) {
            return response()->json(['message' => 'Only clients can create tickets.'], 403);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'client_id' => $user->client->id,
            'category_id' => $request->category_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'attachment' => $attachmentPath,
        ]);

        // Auto-assign to available agent
        $this->autoAssignAgent($ticket);

        $ticket->load(['client.user', 'agent.user', 'category']);

        // TODO: Send notification to assigned agent
        
        return response()->json([
            'message' => 'Ticket created successfully.',
            'ticket' => $ticket,
        ], 201);
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->isClient() && (!$user->client || $ticket->client_id !== $user->client->id)) {
            return response()->json(['message' => 'You can only view your own tickets.'], 403);
        }

        $ticket->load(['client.user', 'agent.user', 'category']);

        return response()->json([
            'ticket' => $ticket,
            'chatify_conversation_id' => $ticket->getChatifyConversationId(),
        ]);
    }

    /**
     * Update the specified ticket.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        
        // Only agents and admins can update tickets
        if (!$user->isAgent() && !$user->isAdmin()) {
            return response()->json(['message' => 'Only agents and admins can update tickets.'], 403);
        }

        $request->validate([
            'status' => 'sometimes|required|in:Open,In Progress,Resolved,Closed',
            'agent_id' => 'sometimes|nullable|exists:agents,id',
            'priority' => 'sometimes|required|in:Low,Medium,High,Critical',
        ]);

        $updateData = $request->only(['status', 'agent_id', 'priority']);

        // Set resolved_at timestamp when marking as resolved/closed
        if (isset($updateData['status']) && in_array($updateData['status'], ['Resolved', 'Closed'])) {
            $updateData['resolved_at'] = now();
        }

        $ticket->update($updateData);
        
        $ticket->load(['client.user', 'agent.user', 'category']);

        // TODO: Send notifications

        return response()->json([
            'message' => 'Ticket updated successfully.',
            'ticket' => $ticket,
        ]);
    }

    /**
     * Remove the specified ticket (Admin only).
     */
    public function destroy(Ticket $ticket)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Only admins can delete tickets.'], 403);
        }

        // Delete attachment if exists
        if ($ticket->attachment) {
            Storage::disk('public')->delete($ticket->attachment);
        }

        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted successfully.',
        ]);
    }

    /**
     * Auto-assign ticket to available agent.
     */
    private function autoAssignAgent(Ticket $ticket)
    {
        // Get available agents for this category
        $availableAgent = Agent::whereHas('categories', function ($query) use ($ticket) {
            $query->where('categories.id', $ticket->category_id);
        })
        ->available()
        ->withCount(['tickets' => function ($query) {
            $query->whereIn('status', ['Open', 'In Progress']);
        }])
        ->orderBy('tickets_count')
        ->first();

        if ($availableAgent) {
            $ticket->update(['agent_id' => $availableAgent->id]);
        }
    }

    /**
     * Get ticket statistics.
     */
    public function stats()
    {
        $user = Auth::user();
        $query = Ticket::query();

        // Filter based on user role
        if ($user->isClient() && $user->client) {
            $query->where('client_id', $user->client->id);
        } elseif ($user->isAgent() && $user->agent) {
            $query->where('agent_id', $user->agent->id);
        }

        $stats = [
            'total' => $query->count(),
            'open' => (clone $query)->where('status', 'Open')->count(),
            'in_progress' => (clone $query)->where('status', 'In Progress')->count(),
            'resolved' => (clone $query)->where('status', 'Resolved')->count(),
            'closed' => (clone $query)->where('status', 'Closed')->count(),
        ];

        return response()->json($stats);
    }
}