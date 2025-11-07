<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffMessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'original_name',
        'path',
        'size',
        'mime_type',
    ];

    public function message()
    {
        return $this->belongsTo(StaffMessage::class, 'message_id');
    }
}
