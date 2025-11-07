<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'first_name', 'middle_name', 'last_name', 'ext_name', 'email', 'contact_number', 'tel_no',
        'department_id', 'course_id', 'organization_id', 'scholarship_id', 'year_level', 'gender', 'birth_date',
        'age', 'civil_status', 'maiden_name', 'place_of_birth', 'nationality', 'religion',
        'complete_home_address', 'street', 'barangay', 'city_municipality', 'province', 'zip_code',
        'student_type1', 'student_type2', 'student_type', 'school_year', 'semester',
        'emergency_contact_name', 'emergency_contact_number', 'emergency_relation',
        'parent_spouse_guardian', 'parent_spouse_guardian_address',
        'spouse_name', 'spouse_contact_no',
        'sport', 'arts', 'technical',
        'elementary_school', 'elementary_address', 'elementary_year_graduated',
        'junior_high_school_name', 'junior_high_school_year_completed', 'junior_high_school_address', 'junior_high_school_honors_awards',
        'senior_high_school_name', 'senior_high_school_year_graduated', 'senior_high_school_track_strand', 'senior_high_school_lrn',
        'senior_high_school_address', 'senior_high_school_honors_awards',
        'high_school', 'high_school_address', 'high_school_year_graduated',
        'college_name', 'college_address', 'college_course', 'college_year',
        'last_school_attended', 'last_school_course', 'last_school_address', 'last_school_year_attended',
        'father_name', 'father_contact_number', 'father_occupation', 'father_workplace', 'father_monthly_income',
        'mother_name', 'mother_contact_number', 'mother_occupation', 'mother_workplace', 'mother_monthly_income',
        'guardian_name', 'guardian_relationship', 'guardian_contact_number', 'guardian_occupation', 'guardian_workplace', 'guardian_monthly_income',
        'is_active_scholar', 'scholarship_grant_name',
        'is_indigenous_group_member', 'indigenous_group_specify',
        'is_pwd', 'pwd_id_image',
        'is_government_member', 'government_level', 'government_role_position',
        'living_arrangement', 'living_arrangement_others_specify',
        'is_single_parent', 'fraternity_sorority_name', 'fraternity_sorority_position', 'has_criminal_record',
        'form_137_presented', 'tor_presented', 'good_moral_cert_presented',
        'birth_cert_presented', 'marriage_cert_presented', 'personal_data_sheet_image'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class, 'course_id');
    }

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class, 'organization_id');
    }

    public function scholarship()
    {
        return $this->belongsTo(\App\Models\Scholarship::class, 'scholarship_id');
    }
}
