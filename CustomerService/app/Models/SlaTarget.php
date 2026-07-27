<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaTarget extends Model
{
    use HasFactory;

    protected $table = 'sla_targets';
    protected $fillable = [
        'priority_level', 'target_time', 'actual_time', 'compliance_percentage',
        'ticket_count', 'status', 'badge_color', 'badge_text_color', 
        'progress_color', 'sort_order'
    ];
}