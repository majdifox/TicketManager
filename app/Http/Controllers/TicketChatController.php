<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

class TicketChatController extends Controller
{
    /**
     * Get the authenticated user from multi-guard setup.
     */
    private function getAuthenticatedUser()
    {
        // Since you have MultiGuardMiddleware, we can directly use Auth::user()
        return Auth::user();
    }

    /**
     * Get chat messages for a ticket.
     */
    public function getMessages(Ticket $ticket)
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check if user can access this ticket
        if (!$this->canAccessTicket($ticket, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the other user in the conversation
        $otherUser = $this->getOtherUser($ticket, $user);
        
        if (!$otherUser) {
            return response()->json(['messages' => [], 'other_user' => null]);
        }

        // Get messages between current user and other user for this specific ticket
        $messages = DB::table('ch_messages')
            ->where(function($query) use ($user, $otherUser) {
                $query->where('from_id', $user->id)
                      ->where('to_id', $otherUser->id);
            })
            ->orWhere(function($query) use ($user, $otherUser) {
                $query->where('from_id', $otherUser->id)
                      ->where('to_id', $user->id);
            })
            ->where('body', 'LIKE', '%#' . $ticket->ticket_number . '%') // Filter by ticket
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as seen
        DB::table('ch_messages')
            ->where('from_id', $otherUser->id)
            ->where('to_id', $user->id)
            ->where('seen', false)
            ->update(['seen' => true, 'updated_at' => now()]);

        return response()->json([
            'messages' => $messages,
            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->avatar ?? '/chatify/images/avatar.png'
            ],
            'ticket' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject
            ]
        ]);
    }

    /**
     * Send a message for a ticket.
     */
    public function sendMessage(Request $request, Ticket $ticket)
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'message' => 'required|string|max:5000'
        ]);

        // Check if user can access this ticket
        if (!$this->canAccessTicket($ticket, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the other user in the conversation
        $otherUser = $this->getOtherUser($ticket, $user);
        
        if (!$otherUser) {
            return response()->json(['error' => 'No agent assigned to this ticket'], 400);
        }

        // Add ticket reference to message
        $messageBody = $request->message . "\n\n[Ticket: #" . $ticket->ticket_number . "]";

        // Create message
        $messageId = Str::uuid();
        $messageData = [
            'id' => $messageId,
            'from_id' => $user->id,
            'to_id' => $otherUser->id,
            'body' => $messageBody,
            'seen' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('ch_messages')->insert($messageData);

        // Broadcast message for real-time updates (if using Pusher)
        try {
            broadcast(new \App\Events\MessageSent($messageData, $otherUser))->toOthers();
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::warning('Failed to broadcast message: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $messageId,
                'from_id' => $user->id,
                'to_id' => $otherUser->id,
                'body' => $request->message, // Return original message without ticket reference
                'seen' => false,
                'created_at' => now()->toISOString(),
                'sender_name' => $user->name
            ]
        ]);
    }

    /**
     * Get chat status for a ticket.
     */
    public function getChatStatus(Ticket $ticket)
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (!$this->canAccessTicket($ticket, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $otherUser = $this->getOtherUser($ticket, $user);
        
        if (!$otherUser) {
            return response()->json([
                'can_chat' => false,
                'reason' => 'No agent assigned to this ticket'
            ]);
        }

        // Check for unread messages
        $unreadCount = DB::table('ch_messages')
            ->where('from_id', $otherUser->id)
            ->where('to_id', $user->id)
            ->where('seen', false)
            ->where('body', 'LIKE', '%#' . $ticket->ticket_number . '%')
            ->count();

        return response()->json([
            'can_chat' => true,
            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'avatar' => $otherUser->avatar ?? '/chatify/images/avatar.png'
            ],
            'unread_count' => $unreadCount,
            'ticket' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject
            ]
        ]);
    }

    /**
     * Mark messages as seen.
     */
    public function markAsSeen(Ticket $ticket)
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Check if user can access this ticket
        if (!$this->canAccessTicket($ticket, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the other user in the conversation
        $otherUser = $this->getOtherUser($ticket, $user);
        
        if (!$otherUser) {
            return response()->json(['success' => false]);
        }

        // Mark messages from other user as seen
        DB::table('ch_messages')
            ->where('from_id', $otherUser->id)
            ->where('to_id', $user->id)
            ->where('seen', false)
            ->where('body', 'LIKE', '%#' . $ticket->ticket_number . '%')
            ->update(['seen' => true, 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Check if user can access the ticket.
     */
    private function canAccessTicket(Ticket $ticket, User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isAgent()) {
            return $ticket->agent_id && $ticket->agent->user_id === $user->id;
        }
        
        if ($user->isClient()) {
            return $ticket->client->user_id === $user->id;
        }
        
        return false;
    }

    /**
     * Get the other user in the conversation.
     */
    private function getOtherUser(Ticket $ticket, User $currentUser)
    {
        if ($currentUser->isClient()) {
            return $ticket->agent ? $ticket->agent->user : null;
        } else {
            return $ticket->client->user;
        }
    }
}