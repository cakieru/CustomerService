<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaWeeklyCompliance extends Model
{
    use HasFactory;

    protected $table = 'sla_weekly_compliance';
    protected $fillable = ['week_name', 'compliance_percentage', 'ticket_count', 'sort_order'];
}