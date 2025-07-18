<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Notifications\TicketCreated;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketStatusUpdated;
use App\Helpers\ChatifyHelper;
use Illuminate\Support\Facades\Storage;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Load relationships to avoid null errors
        $ticket->load(['client.user', 'agent.user']);
        
        // Notify client
        if ($ticket->client && $ticket->client->user) {
            $ticket->client->user->notify(new TicketCreated($ticket));
        }

        // Notify assigned agent if any
        if ($ticket->agent && $ticket->agent->user) {
            $ticket->agent->user->notify(new TicketAssigned($ticket));
            
            // Create Chatify conversation
            ChatifyHelper::createTicketConversation($ticket);
        }

        // Notify all admins
        $admins = \App\Models\User::where('role_id', 1)->get();
        foreach ($admins as $admin) {
            $admin->notify(new TicketCreated($ticket));
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Load relationships to avoid null errors
        $ticket->load(['client.user', 'agent.user']);
        
        // Get original values using getOriginal() method
        $originalStatus = $ticket->getOriginal('status');
        $originalAgentId = $ticket->getOriginal('agent_id');

        // Check if status changed
        if ($originalStatus !== $ticket->status) {
            // Notify client
            if ($ticket->client && $ticket->client->user) {
                $ticket->client->user->notify(new TicketStatusUpdated($ticket, $originalStatus));
            }

            // Notify agent if assigned
            if ($ticket->agent && $ticket->agent->user) {
                $ticket->agent->user->notify(new TicketStatusUpdated($ticket, $originalStatus));
            }

            // Send system message in chat
            $statusMessage = "Ticket status changed from {$originalStatus} to {$ticket->status}";
            ChatifyHelper::sendSystemMessage($ticket, $statusMessage);
        }

        // Check if agent changed
        if ($originalAgentId !== $ticket->agent_id && $ticket->agent_id) {
            // Refresh the agent relationship to get the new agent
            $ticket->load('agent.user');
            
            // Notify new agent - add null check
            if ($ticket->agent && $ticket->agent->user) {
                $ticket->agent->user->notify(new TicketAssigned($ticket));

                // Create or update Chatify conversation
                ChatifyHelper::createTicketConversation($ticket);

                // Send system message
                $assignMessage = "Ticket assigned to {$ticket->agent->user->name}";
                ChatifyHelper::sendSystemMessage($ticket, $assignMessage);
            }
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        // Clean up attachments
        if ($ticket->attachment) {
            Storage::disk('public')->delete($ticket->attachment);
        }
    }
}