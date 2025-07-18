<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'active_status',
        'avatar',
        'dark_mode',
        'messenger_color',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active_status' => 'boolean',
        'dark_mode' => 'boolean',
    ];

    /**
     * Boot method to set default values for Chatify compatibility.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            // Set default values for Chatify fields if not provided
            if (is_null($user->messenger_color)) {
                $user->messenger_color = '#2180f3';
            }
            if (is_null($user->active_status)) {
                $user->active_status = true;
            }
        });
    }

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the admin profile for the user.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Get the agent profile for the user.
     */
    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    /**
     * Get the client profile for the user.
     */
    public function client()
    {
        return $this->hasOne(Client::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    /**
     * Check if user is an agent.
     */
    public function isAgent(): bool
    {
        return $this->role_id === 2;
    }

    /**
     * Check if user is a client.
     */
    public function isClient(): bool
    {
        return $this->role_id === 3;
    }

    /**
     * Get the role-specific profile based on role_id.
     */
    public function getRoleSpecificProfile()
    {
        switch ($this->role_id) {
            case 1:
                return $this->admin;
            case 2:
                return $this->agent;
            case 3:
                return $this->client;
            default:
                return null;
        }
    }

    /**
     * Get redirect path after login based on role.
     */
    public function getRedirectPath(): string
    {
        switch ($this->role_id) {
            case 1:
                return '/admin/dashboard';
            case 2:
                return '/agent/dashboard';
            case 3:
                return '/client/dashboard';
            default:
                return '/';
        }
    }

    /**
     * Get the notifications for the user.
     * This method is required for Laravel's notification system.
     */
    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get the unread notifications for the user.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    /**
     * Get the read notifications for the user.
     */
    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }

    /**
     * Chatify compatibility methods
     */
    public function getAvatar()
    {
        return $this->avatar ?? asset('chatify/images/avatar.png');
    }

    public function getMessengerColor()
    {
        return $this->messenger_color ?? '#2180f3';
    }
}