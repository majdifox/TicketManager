<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Notifications\TicketCreated;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketStatusUpdated;
use App\Helpers\ChatifyHelper;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Notify client
        $ticket->client->user->notify(new TicketCreated($ticket));

        // Notify assigned agent if any
        if ($ticket->agent) {
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
     * Handle the Ticket "updating" event.
     */
    public function updating(Ticket $ticket): void
    {
        // Store original values before update
        $ticket->original_status = $ticket->getOriginal('status');
        $ticket->original_agent_id = $ticket->getOriginal('agent_id');
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Check if status changed
        if ($ticket->original_status !== $ticket->status) {
            // Notify client
            $ticket->client->user->notify(new TicketStatusUpdated($ticket, $ticket->original_status));

            // Notify agent if assigned
            if ($ticket->agent) {
                $ticket->agent->user->notify(new TicketStatusUpdated($ticket, $ticket->original_status));
            }

            // Send system message in chat
            $statusMessage = "Ticket status changed from {$ticket->original_status} to {$ticket->status}";
            ChatifyHelper::sendSystemMessage($ticket, $statusMessage);
        }

        // Check if agent changed
        if ($ticket->original_agent_id !== $ticket->agent_id && $ticket->agent_id) {
            // Notify new agent
            $ticket->agent->user->notify(new TicketAssigned($ticket));

            // Create or update Chatify conversation
            ChatifyHelper::createTicketConversation($ticket);

            // Send system message
            $assignMessage = "Ticket assigned to {$ticket->agent->user->name}";
            ChatifyHelper::sendSystemMessage($ticket, $assignMessage);
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        // Clean up attachments
        if ($ticket->attachment) {
            \Storage::disk('public')->delete($ticket->attachment);
        }
    }
}