<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_special', 'department_id', 'official_email', 'acronym', 'mailing_address', 'date_established'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get users who belong to this organization via the pivot table (additional organizations).
     */
    public function otherUsers()
    {
        return $this->belongsToMany(User::class, 'organization_user', 'organization_id', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}