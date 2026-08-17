<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteAudit extends Model
{
    protected $fillable = [
        'website_id',
        'url',
        'status',
        'seo_score',
        'security_score',
        'performance_score',
        'accessibility_score',
        'total_score',
        'report_data',
        'ai_summary',
        'cross_intelligence_report',
        'error_message'
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    protected $casts = [
        'report_data' => 'array',
    ];
}
