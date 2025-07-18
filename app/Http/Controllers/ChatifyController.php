<?php

namespace App\Http\Controllers;

use Chatify\Http\Controllers\MessagesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatifyController extends MessagesController
{
    /**
     * Get the authenticated user from multi-guard setup.
     */
    protected function getAuthenticatedUser()
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
     * Override the index method to use multi-guard authentication.
     */
    public function index($id = null)
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return redirect()->route('client.login');
        }

        // Set the authenticated user in the default auth guard for Chatify
        Auth::login($user);
        
        // Ensure user has Chatify compatible fields using DB update
        if (is_null($user->messenger_color)) {
            DB::table('users')->where('id', $user->id)->update(['messenger_color' => '#2180f3']);
            $user->messenger_color = '#2180f3';
        }
        
        if (is_null($user->active_status)) {
            DB::table('users')->where('id', $user->id)->update(['active_status' => true]);
            $user->active_status = true;
        }

        return parent::index($id);
    }

    /**
     * Override other methods as needed for multi-guard support
     */
}