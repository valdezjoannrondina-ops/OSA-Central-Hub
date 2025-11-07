<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'requirement_name', 'is_uploaded', 'uploaded_by', 'file_path'
    ];

    protected $casts = [
        'is_uploaded' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}