<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppointmentFile extends Model
{
    use HasFactory;

    protected $fillable = ['appointment_id', 'file_name', 'file_path', 'uploaded_by'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}