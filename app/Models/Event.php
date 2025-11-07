<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    public function requirements()
    {
        return $this->hasMany(EventRequirement::class);
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }
    protected $fillable = [
        'name', 'description', 'event_date', 'end_date', 'location', 'organization_id', 'status', 'start_time', 'end_time', 'qr_code_path', 'created_by', 'decline_reason'
    ];

    protected $casts = [
        'event_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function attendances() {
        return $this->hasMany(Attendance::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organization() {
        return $this->belongsTo(Organization::class);
    }
}