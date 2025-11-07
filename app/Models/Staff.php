<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'user_id',
        'email',
        'designation',
        'department_id',
        'organization_id',
        'admin_id',
        'contact_number',
        'image',
        'service_order',
        'birth_date',
        'gender',
        'age',
        'length_of_service',
        'contract_end_at',
        'employment_status',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_staff', 'staff_id', 'organization_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
