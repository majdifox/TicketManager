<?php

namespace App\Helpers;

use App\Models\Ticket;
use App\Models\User;
use Chatify\Facades\ChatifyMessenger as Chatify;

class ChatifyHelper
{
    /**
     * Create a conversation for a ticket between client and agent.
     */
    public static function createTicketConversation(Ticket $ticket)
    {
        if (!$ticket->agent_id) {
            return null;
        }

        $clientUser = $ticket->client->user;
        $agentUser = $ticket->agent->user;

        // Send initial message from system
        $message = "Ticket #{$ticket->ticket_number} - {$ticket->subject}\n\n";
        $message .= "Priority: {$ticket->priority}\n";
        $message .= "Category: {$ticket->category->name}\n\n";
        $message .= "Description: {$ticket->description}";

        // Use Chatify to send message
        // Note: Chatify doesn't have a direct API for creating conversations
        // Messages create conversations automatically
        
        return [
            'client_id' => $clientUser->id,
            'agent_id' => $agentUser->id,
            'ticket_id' => $ticket->id,
            'initial_message' => $message,
        ];
    }

    /**
     * Get Chatify URL for a ticket conversation.
     */
    public static function getTicketChatUrl(Ticket $ticket, User $currentUser)
    {
        $otherUserId = null;

        if ($currentUser->isClient()) {
            $otherUserId = $ticket->agent ? $ticket->agent->user_id : null;
        } elseif ($currentUser->isAgent() || $currentUser->isAdmin()) {
            $otherUserId = $ticket->client->user_id;
        }

        if (!$otherUserId) {
            return null;
        }

        return route('chatify', ['user' => $otherUserId]);
    }

    /**
     * Send a system notification message in the chat.
     */
    public static function sendSystemMessage(Ticket $ticket, string $message)
    {
        if (!$ticket->agent_id) {
            return;
        }

        // This would need to be implemented based on Chatify's internal structure
        // For now, it's a placeholder for the concept
        
        $clientUserId = $ticket->client->user_id;
        $agentUserId = $ticket->agent->user_id;

        // Log the system message for both users
        // In a real implementation, you'd use Chatify's message creation
    }
}