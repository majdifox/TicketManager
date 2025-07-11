<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'department',
        'phone',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_available' => 'boolean',
    ];

    /**
     * Get the user that owns the agent profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tickets for the agent.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * The categories that belong to the agent.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_agent');
    }

    /**
     * Scope a query to only include available agents.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Get agent workload (number of open tickets).
     */
    public function getOpenTicketsCountAttribute()
    {
        return $this->tickets()
            ->whereIn('status', ['Open', 'In Progress'])
            ->count();
    }
}