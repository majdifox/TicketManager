<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Http\Requests\CreateTicketRequest;
use App\Http\Requests\UpdateTicketRequest;

class TicketController extends Controller
{
    /**
     * Display tickets for admin.
     */
    public function adminIndex(Request $request)
    {
        $query = Ticket::with(['client.user', 'agent.user', 'category']);

        // Apply filters
        $this->applyFilters($query, $request);

        $tickets = $query->latest()->paginate(15)->appends($request->all());

        return Inertia::render('Admin/Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status', 'priority', 'category_id', 'search']),
            'categories' => Category::active()->get(),
            'statuses' => Ticket::getStatuses(),
            'priorities' => Ticket::getPriorities(),
        ]);
    }

    /**
     * Display tickets for agent.
     */
    public function agentIndex(Request $request)
    {
        $agent = Auth::guard('agent')->user()->agent;
        $query = Ticket::where('agent_id', $agent->id)
            ->with(['client.user', 'category']);

        $this->applyFilters($query, $request);

        $tickets = $query->latest()->paginate(15)->appends($request->all());

        return Inertia::render('Agent/Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status', 'priority', 'search']),
            'statuses' => Ticket::getStatuses(),
            'priorities' => Ticket::getPriorities(),
        ]);
    }

    /**
     * Display tickets for client.
     */
    public function clientIndex(Request $request)
    {
        $client = Auth::guard('client')->user()->client;
        $query = Ticket::where('client_id', $client->id)
            ->with(['agent.user', 'category']);

        $this->applyFilters($query, $request);

        $tickets = $query->latest()->paginate(15)->appends($request->all());

        return Inertia::render('Client/Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status', 'priority', 'search']),
            'statuses' => Ticket::getStatuses(),
            'priorities' => Ticket::getPriorities(),
        ]);
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        return Inertia::render('Client/Tickets/Create', [
            'categories' => Category::active()->get(),
            'priorities' => Ticket::getPriorities(),
        ]);
    }

    /**
     * Store a newly created ticket.
     */
    public function store(CreateTicketRequest $request)
    {
        $client = Auth::guard('client')->user()->client;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'client_id' => $client->id,
            'category_id' => $request->category_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'attachment' => $attachmentPath,
        ]);

        // Auto-assign to available agent
        $this->autoAssignAgent($ticket);

        return redirect()->route('client.tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
{
    $this->authorizeTicketAccess($ticket);

    $ticket->load([
        'client.user', 
        'agent.user', 
        'category'
    ]);

    // Get the appropriate view based on user role
    $view = 'Client/Tickets/Show';
    if (Auth::guard('admin')->check()) {
        $view = 'Admin/Tickets/Show';
    } elseif (Auth::guard('agent')->check()) {
        $view = 'Agent/Tickets/Show';
    }

    return Inertia::render($view, [
        'ticket' => $ticket,
        'chatifyConversationId' => $ticket->getChatifyConversationId(),
        'statuses' => Ticket::getStatuses(),
        'priorities' => Ticket::getPriorities(),
        'agents' => Auth::guard('admin')->check() ? Agent::with('user')->get() : null,
    ]);
}

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit(Ticket $ticket)
    {
        $this->authorizeTicketAccess($ticket);

        $ticket->load(['client.user', 'agent.user', 'category']);
        
        $categories = Category::active()->get();
        $agents = Agent::with('user')->available()->get();

        return Inertia::render('Admin/Tickets/Edit', [
            'ticket' => $ticket,
            'categories' => $categories,
            'agents' => $agents,
        ]);
    }

    /**
     * Update the specified ticket.
     */
       public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        // Ensure admin/agent can access this ticket
        $this->authorizeTicketAccess($ticket);

        $updateData = $request->validated();

        // Set resolved_at timestamp when marking as resolved/closed
        if (isset($updateData['status']) && in_array($updateData['status'], ['Resolved', 'Closed'])) {
            $updateData['resolved_at'] = now();
        }

        // If status is being changed back to Open or In Progress, clear resolved_at
        if (isset($updateData['status']) && in_array($updateData['status'], ['Open', 'In Progress'])) {
            $updateData['resolved_at'] = null;
        }

        $ticket->update($updateData);

        // Redirect based on user type
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.tickets.show', $ticket)
                ->with('success', 'Ticket updated successfully.');
        } elseif (Auth::guard('agent')->check()) {
            return redirect()->route('agent.tickets.show', $ticket)
                ->with('success', 'Ticket updated successfully.');
        }

        return redirect()->back()->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified ticket (Admin only).
     */
    public function destroy(Ticket $ticket)
    {
        // Delete attachment if exists
        if ($ticket->attachment) {
            Storage::disk('public')->delete($ticket->attachment);
        }

        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        $query->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        })
        ->when($request->priority, function ($q, $priority) {
            return $q->where('priority', $priority);
        })
        ->when($request->category_id, function ($q, $category) {
            return $q->where('category_id', $category);
        })
        ->when($request->search, function ($q, $search) {
            return $q->where(function ($query) use ($search) {
                $query->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        });
    }

    /**
     * Authorize ticket access based on user role.
     */
       private function authorizeTicketAccess(Ticket $ticket)
    {
        if (Auth::guard('admin')->check()) {
            // Admins can view and update all tickets
            return;
        } elseif (Auth::guard('agent')->check()) {
            $agent = Auth::guard('agent')->user()->agent;
            if ($ticket->agent_id !== $agent->id) {
                abort(403, 'You can only view tickets assigned to you.');
            }
        } elseif (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user()->client;
            if ($ticket->client_id !== $client->id) {
                abort(403, 'You can only view your own tickets.');
            }
        } else {
            abort(403, 'Unauthorized access.');
        }
    }

    /**
     * Auto-assign ticket to available agent.
     */
    private function autoAssignAgent(Ticket $ticket)
    {
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
}