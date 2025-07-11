<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get user's notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->when($request->unread_only, function ($query) {
                return $query->whereNull('read_at');
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read.',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
     * Delete notification.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted.',
        ]);
    }

    /**
     * Get notification preferences.
     */
    public function preferences()
    {
        $user = Auth::user();
        
        // You can store preferences in user table or separate table
        $preferences = [
            'email_notifications' => true,
            'ticket_created' => true,
            'ticket_assigned' => true,
            'ticket_updated' => true,
            'ticket_resolved' => true,
            'new_message' => true,
        ];

        return response()->json($preferences);
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'ticket_created' => 'boolean',
            'ticket_assigned' => 'boolean',
            'ticket_updated' => 'boolean',
            'ticket_resolved' => 'boolean',
            'new_message' => 'boolean',
        ]);

        // Store preferences logic here
        // This is a placeholder - implement based on your preference storage method

        return response()->json([
            'message' => 'Notification preferences updated.',
        ]);
    }
}