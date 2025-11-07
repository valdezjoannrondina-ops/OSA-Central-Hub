<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusChange extends Model
{
    protected $fillable = [
        'auditable_type', 'auditable_id', 'from_status', 'to_status', 'changed_by', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}


