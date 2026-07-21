<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaPriorityPerformance extends Model
{
    use HasFactory;

    protected $table = 'sla_priority_performance';
    protected $fillable = ['priority_level', 'compliance_percentage', 'color_class', 'sort_order'];
}