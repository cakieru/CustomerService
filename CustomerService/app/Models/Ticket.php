<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['ticket_reference', 'user_id', 'agent_id', 'subject', 'description', 'category', 'status', 'priority', 'due_date'];

    protected $casts = [
        'due_date' => 'datetime',
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
    
    // Remove the old getAttachmentsAttribute() block and insert this:
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }
}