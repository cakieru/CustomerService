<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaMetric extends Model
{
    use HasFactory;

    protected $table = 'sla_metrics';
    protected $fillable = [
        'metric_name', 'metric_value', 'metric_label', 'icon_class',
        'icon_bg', 'trend_text', 'trend_direction', 'target_value'
    ];
}