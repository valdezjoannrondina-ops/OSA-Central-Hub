<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistantLeadershipBackground extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization',
        'position',
        'year',
        'order',
    ];

    /**
     * Get the user that owns this leadership background entry.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

