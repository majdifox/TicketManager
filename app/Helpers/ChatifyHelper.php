<?php

namespace App\Helpers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Create initial system message
        $message = "🎫 Ticket #{$ticket->ticket_number}\n";
        $message .= "📋 Subject: {$ticket->subject}\n";
        $message .= "🔥 Priority: {$ticket->priority}\n";
        $message .= "📂 Category: {$ticket->category->name}\n\n";
        $message .= "📝 Description: {$ticket->description}";

        // Create message in Chatify format
        try {
            DB::table('ch_messages')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'from_id' => $agentUser->id,
                'to_id' => $clientUser->id,
                'body' => $message,
                'seen' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create Chatify conversation: ' . $e->getMessage());
        }

        return [
            'client_id' => $clientUser->id,
            'agent_id' => $agentUser->id,
            'ticket_id' => $ticket->id,
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

        return url("/chatify/{$otherUserId}");
    }

    /**
     * Send a system notification message in the chat.
     */
    public static function sendSystemMessage(Ticket $ticket, string $message)
    {
        if (!$ticket->agent_id) {
            return;
        }

        $clientUserId = $ticket->client->user_id;
        $agentUserId = $ticket->agent->user_id;

        $systemMessage = "🔔 System: " . $message;

        try {
            // Send message to both users
            DB::table('ch_messages')->insert([
                [
                    'id' => \Illuminate\Support\Str::uuid(),
                    'from_id' => $agentUserId,
                    'to_id' => $clientUserId,
                    'body' => $systemMessage,
                    'seen' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send system message: ' . $e->getMessage());
        }
    }
}