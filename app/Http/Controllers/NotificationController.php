<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Get the authenticated user from any guard.
     */
    private function getAuthenticatedUser()
    {
        // Check each guard to find the authenticated user
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        }
        
        if (Auth::guard('agent')->check()) {
            return Auth::guard('agent')->user();
        }
        
        if (Auth::guard('client')->check()) {
            return Auth::guard('client')->user();
        }
        
        // Fallback to default guard
        return Auth::user();
    }

    /**
     * Get the current guard name.
     */
    private function getCurrentGuard()
    {
        if (Auth::guard('admin')->check()) {
            return 'admin';
        }
        
        if (Auth::guard('agent')->check()) {
            return 'agent';
        }
        
        if (Auth::guard('client')->check()) {
            return 'client';
        }
        
        return 'web';
    }

    /**
     * Display notifications page (Inertia).
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            abort(401, 'Unauthenticated');
        }
        
        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);

        $unreadCount = $user->unreadNotifications()->count();

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get user's notifications for AJAX requests (JSON).
     */
    public function fetch(Request $request)
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        $notifications = $user->notifications()
            ->when($request->unread_only, function ($query) {
                return $query->whereNull('read_at');
            })
            ->latest()
            ->take(10) // Limit for dropdown
            ->get();

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
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
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
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
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
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
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
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
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
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
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