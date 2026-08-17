<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'status',
        'page_count',
        'processing_error',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
