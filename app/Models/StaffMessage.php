<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'type', // 'announcement' or 'inquiry'
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(StaffMessage::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(StaffMessage::class, 'parent_id');
    }

    public function attachments()
    {
        return $this->hasMany(StaffMessageAttachment::class, 'message_id');
    }

    public function mentions()
    {
        return $this->belongsToMany(User::class, 'staff_message_mentions', 'message_id', 'mentioned_user_id');
    }
}
