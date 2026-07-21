<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_reference', 
        'user_id', 
        'agent_id', 
        'subject', 
        'description', 
        'category', 
        'status', 
        'priority', 
        'due_date',
        'responded_at',
        'resolved_at',
        'customer_name',
        'issue_description',
        'priority_level',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Alias for SLA compatibility
     */
    public function getPriorityLevelAttribute()
    {
        return $this->priority_level ?? $this->priority;
    }

    /**
     * Check if this ticket is tracked by SLA system
     */
    public function scopeSlaTrackable($query)
    {
        return $query->whereNotNull('priority_level')
                     ->orWhereNotNull('priority');
    }
}