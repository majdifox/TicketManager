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
}