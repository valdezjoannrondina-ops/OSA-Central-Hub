<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add Student Information Sheet columns to users table
        Schema::table('users', function (Blueprint $table) {
            // A. NAME
            if (!Schema::hasColumn('users', 'ext_name')) {
                $table->string('ext_name', 50)->nullable()->after('last_name');
            }
            
            // B. HOME ADDRESS
            if (!Schema::hasColumn('users', 'street')) {
                $table->text('street')->nullable()->after('complete_home_address');
            }
            if (!Schema::hasColumn('users', 'barangay')) {
                $table->text('barangay')->nullable();
            }
            if (!Schema::hasColumn('users', 'city_municipality')) {
                $table->text('city_municipality')->nullable();
            }
            if (!Schema::hasColumn('users', 'province')) {
                $table->text('province')->nullable();
            }
            if (!Schema::hasColumn('users', 'zip_code')) {
                $table->string('zip_code', 20)->nullable();
            }
            
            // C. PERSONAL DETAILS
            if (!Schema::hasColumn('users', 'nationality')) {
                $table->text('nationality')->nullable()->after('civil_status');
            }
            
            // D. Other
            if (!Schema::hasColumn('users', 'religion')) {
                $table->text('religion')->nullable();
            }
            if (!Schema::hasColumn('users', 'tel_no')) {
                $table->string('tel_no', 50)->nullable()->after('contact_number');
            }
            if (!Schema::hasColumn('users', 'spouse_name')) {
                $table->text('spouse_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'spouse_contact_no')) {
                $table->string('spouse_contact_no', 50)->nullable();
            }
            
            // E. SPECIAL SKILLS AND TALENTS
            if (!Schema::hasColumn('users', 'sport')) {
                $table->text('sport')->nullable();
            }
            if (!Schema::hasColumn('users', 'arts')) {
                $table->text('arts')->nullable();
            }
            if (!Schema::hasColumn('users', 'technical')) {
                $table->text('technical')->nullable();
            }
            
            // F. EDUCATION BACKGROUND
            if (!Schema::hasColumn('users', 'junior_high_school_name')) {
                $table->text('junior_high_school_name')->nullable()->after('elementary_year_graduated');
            }
            if (!Schema::hasColumn('users', 'junior_high_school_year_completed')) {
                $table->string('junior_high_school_year_completed', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'junior_high_school_address')) {
                $table->text('junior_high_school_address')->nullable();
            }
            if (!Schema::hasColumn('users', 'junior_high_school_honors_awards')) {
                $table->text('junior_high_school_honors_awards')->nullable();
            }
            if (!Schema::hasColumn('users', 'senior_high_school_name')) {
                $table->text('senior_high_school_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'senior_high_school_year_graduated')) {
                $table->string('senior_high_school_year_graduated', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'senior_high_school_track_strand')) {
                $table->text('senior_high_school_track_strand')->nullable();
            }
            if (!Schema::hasColumn('users', 'senior_high_school_lrn')) {
                $table->string('senior_high_school_lrn', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'senior_high_school_address')) {
                $table->text('senior_high_school_address')->nullable();
            }
            if (!Schema::hasColumn('users', 'senior_high_school_honors_awards')) {
                $table->text('senior_high_school_honors_awards')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_school_attended')) {
                $table->text('last_school_attended')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_school_course')) {
                $table->text('last_school_course')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_school_address')) {
                $table->text('last_school_address')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_school_year_attended')) {
                $table->string('last_school_year_attended', 20)->nullable();
            }
            
            // G. FAMILY BACKGROUND
            if (!Schema::hasColumn('users', 'father_name')) {
                $table->text('father_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'father_contact_number')) {
                $table->string('father_contact_number', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'father_occupation')) {
                $table->text('father_occupation')->nullable();
            }
            if (!Schema::hasColumn('users', 'father_workplace')) {
                $table->text('father_workplace')->nullable();
            }
            if (!Schema::hasColumn('users', 'father_monthly_income')) {
                $table->text('father_monthly_income')->nullable();
            }
            if (!Schema::hasColumn('users', 'mother_name')) {
                $table->text('mother_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'mother_contact_number')) {
                $table->string('mother_contact_number', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'mother_occupation')) {
                $table->text('mother_occupation')->nullable();
            }
            if (!Schema::hasColumn('users', 'mother_workplace')) {
                $table->text('mother_workplace')->nullable();
            }
            if (!Schema::hasColumn('users', 'mother_monthly_income')) {
                $table->text('mother_monthly_income')->nullable();
            }
            if (!Schema::hasColumn('users', 'guardian_name')) {
                $table->text('guardian_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'guardian_relationship')) {
                $table->text('guardian_relationship')->nullable();
            }
            if (!Schema::hasColumn('users', 'guardian_contact_number')) {
                $table->string('guardian_contact_number', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'guardian_occupation')) {
                $table->text('guardian_occupation')->nullable();
            }
            if (!Schema::hasColumn('users', 'guardian_workplace')) {
                $table->text('guardian_workplace')->nullable();
            }
            if (!Schema::hasColumn('users', 'guardian_monthly_income')) {
                $table->text('guardian_monthly_income')->nullable();
            }
            
            // H. OTHER INFORMATION
            if (!Schema::hasColumn('users', 'is_active_scholar')) {
                $table->boolean('is_active_scholar')->default(false)->nullable();
            }
            if (!Schema::hasColumn('users', 'scholarship_grant_name')) {
                $table->text('scholarship_grant_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_indigenous_group_member')) {
                $table->boolean('is_indigenous_group_member')->default(false)->nullable();
            }
            if (!Schema::hasColumn('users', 'indigenous_group_specify')) {
                $table->text('indigenous_group_specify')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_pwd')) {
                $table->boolean('is_pwd')->default(false)->nullable();
            }
            if (!Schema::hasColumn('users', 'pwd_id_image')) {
                $table->text('pwd_id_image')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_government_member')) {
                $table->enum('is_government_member', ['no', 'yes'])->nullable();
            }
            if (!Schema::hasColumn('users', 'government_level')) {
                $table->enum('government_level', ['barangay', 'municipal_city', 'provincial'])->nullable();
            }
            if (!Schema::hasColumn('users', 'government_role_position')) {
                $table->text('government_role_position')->nullable();
            }
            if (!Schema::hasColumn('users', 'living_arrangement')) {
                $table->enum('living_arrangement', ['home', 'boarding_house', 'relatives', 'working_student', 'others'])->nullable();
            }
            if (!Schema::hasColumn('users', 'living_arrangement_others_specify')) {
                $table->text('living_arrangement_others_specify')->nullable();
            }
        });

        // Add Student Information Sheet columns to students table
        Schema::table('students', function (Blueprint $table) {
            // A. NAME
            if (!Schema::hasColumn('students', 'ext_name')) {
                $table->string('ext_name', 50)->nullable()->after('last_name');
            }
            
            // B. HOME ADDRESS
            if (!Schema::hasColumn('students', 'street')) {
                $table->text('street')->nullable()->after('complete_home_address');
            }
            if (!Schema::hasColumn('students', 'barangay')) {
                $table->text('barangay')->nullable();
            }
            if (!Schema::hasColumn('students', 'city_municipality')) {
                $table->text('city_municipality')->nullable();
            }
            if (!Schema::hasColumn('students', 'province')) {
                $table->text('province')->nullable();
            }
            if (!Schema::hasColumn('students', 'zip_code')) {
                $table->string('zip_code', 20)->nullable();
            }
            
            // C. PERSONAL DETAILS
            if (!Schema::hasColumn('students', 'nationality')) {
                $table->text('nationality')->nullable()->after('civil_status');
            }
            
            // D. Other
            if (!Schema::hasColumn('students', 'religion')) {
                $table->text('religion')->nullable();
            }
            if (!Schema::hasColumn('students', 'tel_no')) {
                $table->string('tel_no', 50)->nullable()->after('contact_number');
            }
            if (!Schema::hasColumn('students', 'spouse_name')) {
                $table->text('spouse_name')->nullable();
            }
            if (!Schema::hasColumn('students', 'spouse_contact_no')) {
                $table->string('spouse_contact_no', 50)->nullable();
            }
            
            // E. SPECIAL SKILLS AND TALENTS
            if (!Schema::hasColumn('students', 'sport')) {
                $table->text('sport')->nullable();
            }
            if (!Schema::hasColumn('students', 'arts')) {
                $table->text('arts')->nullable();
            }
            if (!Schema::hasColumn('students', 'technical')) {
                $table->text('technical')->nullable();
            }
            
            // F. EDUCATION BACKGROUND
            if (!Schema::hasColumn('students', 'junior_high_school_name')) {
                $table->text('junior_high_school_name')->nullable()->after('elementary_year_graduated');
            }
            if (!Schema::hasColumn('students', 'junior_high_school_year_completed')) {
                $table->string('junior_high_school_year_completed', 20)->nullable();
            }
            if (!Schema::hasColumn('students', 'junior_high_school_address')) {
                $table->text('junior_high_school_address')->nullable();
            }
            if (!Schema::hasColumn('students', 'junior_high_school_honors_awards')) {
                $table->text('junior_high_school_honors_awards')->nullable();
            }
            if (!Schema::hasColumn('students', 'senior_high_school_name')) {
                $table->text('senior_high_school_name')->nullable();
            }
            if (!Schema::hasColumn('students', 'senior_high_school_year_graduated')) {
                $table->string('senior_high_school_year_graduated', 20)->nullable();
            }
            if (!Schema::hasColumn('students', 'senior_high_school_track_strand')) {
                $table->text('senior_high_school_track_strand')->nullable();
            }
            if (!Schema::hasColumn('students', 'senior_high_school_lrn')) {
                $table->string('senior_high_school_lrn', 50)->nullable();
            }
            if (!Schema::hasColumn('students', 'senior_high_school_address')) {
                $table->text('senior_high_school_address')->nullable();
            }
            if (!Schema::hasColumn('students', 'senior_high_school_honors_awards')) {
                $table->text('senior_high_school_honors_awards')->nullable();
            }
            if (!Schema::hasColumn('students', 'last_school_attended')) {
                $table->text('last_school_attended')->nullable();
            }
            if (!Schema::hasColumn('students', 'last_school_course')) {
                $table->text('last_school_course')->nullable();
            }
            if (!Schema::hasColumn('students', 'last_school_address')) {
                $table->text('last_school_address')->nullable();
            }
            if (!Schema::hasColumn('students', 'last_school_year_attended')) {
                $table->string('last_school_year_attended', 20)->nullable();
            }
            
            // G. FAMILY BACKGROUND
            if (!Schema::hasColumn('students', 'father_name')) {
                $table->text('father_name')->nullable();
            }
            if (!Schema::hasColumn('students', 'father_contact_number')) {
                $table->string('father_contact_number', 50)->nullable();
            }
            if (!Schema::hasColumn('students', 'father_occupation')) {
                $table->text('father_occupation')->nullable();
            }
            if (!Schema::hasColumn('students', 'father_workplace')) {
                $table->text('father_workplace')->nullable();
            }
            if (!Schema::hasColumn('students', 'father_monthly_income')) {
                $table->text('father_monthly_income')->nullable();
            }
            if (!Schema::hasColumn('students', 'mother_name')) {
                $table->text('mother_name')->nullable();
            }
            if (!Schema::hasColumn('students', 'mother_contact_number')) {
                $table->string('mother_contact_number', 50)->nullable();
            }
            if (!Schema::hasColumn('students', 'mother_occupation')) {
                $table->text('mother_occupation')->nullable();
            }
            if (!Schema::hasColumn('students', 'mother_workplace')) {
                $table->text('mother_workplace')->nullable();
            }
            if (!Schema::hasColumn('students', 'mother_monthly_income')) {
                $table->text('mother_monthly_income')->nullable();
            }
            if (!Schema::hasColumn('students', 'guardian_name')) {
                $table->text('guardian_name')->nullable();
            }
            if (!Schema::hasColumn('students', 'guardian_relationship')) {
                $table->text('guardian_relationship')->nullable();
            }
            if (!Schema::hasColumn('students', 'guardian_contact_number')) {
                $table->string('guardian_contact_number', 50)->nullable();
            }
            if (!Schema::hasColumn('students', 'guardian_occupation')) {
                $table->text('guardian_occupation')->nullable();
            }
            if (!Schema::hasColumn('students', 'guardian_workplace')) {
                $table->text('guardian_workplace')->nullable();
            }
            if (!Schema::hasColumn('students', 'guardian_monthly_income')) {
                $table->text('guardian_monthly_income')->nullable();
            }
            
            // H. OTHER INFORMATION
            if (!Schema::hasColumn('students', 'is_active_scholar')) {
                $table->boolean('is_active_scholar')->default(false)->nullable();
            }
            if (!Schema::hasColumn('students', 'scholarship_grant_name')) {
                $table->text('scholarship_grant_name')->nullable();
            }
            if (!Schema::hasColumn('students', 'is_indigenous_group_member')) {
                $table->boolean('is_indigenous_group_member')->default(false)->nullable();
            }
            if (!Schema::hasColumn('students', 'indigenous_group_specify')) {
                $table->text('indigenous_group_specify')->nullable();
            }
            if (!Schema::hasColumn('students', 'is_pwd')) {
                $table->boolean('is_pwd')->default(false)->nullable();
            }
            if (!Schema::hasColumn('students', 'pwd_id_image')) {
                $table->text('pwd_id_image')->nullable();
            }
            if (!Schema::hasColumn('students', 'is_government_member')) {
                $table->enum('is_government_member', ['no', 'yes'])->nullable();
            }
            if (!Schema::hasColumn('students', 'government_level')) {
                $table->enum('government_level', ['barangay', 'municipal_city', 'provincial'])->nullable();
            }
            if (!Schema::hasColumn('students', 'government_role_position')) {
                $table->text('government_role_position')->nullable();
            }
            if (!Schema::hasColumn('students', 'living_arrangement')) {
                $table->enum('living_arrangement', ['home', 'boarding_house', 'relatives', 'working_student', 'others'])->nullable();
            }
            if (!Schema::hasColumn('students', 'living_arrangement_others_specify')) {
                $table->text('living_arrangement_others_specify')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Student Information Sheet columns from users table
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'ext_name',
                'street', 'barangay', 'city_municipality', 'province', 'zip_code',
                'nationality',
                'religion', 'tel_no', 'spouse_name', 'spouse_contact_no',
                'sport', 'arts', 'technical',
                'junior_high_school_name', 'junior_high_school_year_completed', 'junior_high_school_address', 'junior_high_school_honors_awards',
                'senior_high_school_name', 'senior_high_school_year_graduated', 'senior_high_school_track_strand', 'senior_high_school_lrn',
                'senior_high_school_address', 'senior_high_school_honors_awards',
                'last_school_attended', 'last_school_course', 'last_school_address', 'last_school_year_attended',
                'father_name', 'father_contact_number', 'father_occupation', 'father_workplace', 'father_monthly_income',
                'mother_name', 'mother_contact_number', 'mother_occupation', 'mother_workplace', 'mother_monthly_income',
                'guardian_name', 'guardian_relationship', 'guardian_contact_number', 'guardian_occupation', 'guardian_workplace', 'guardian_monthly_income',
                'is_active_scholar', 'scholarship_grant_name',
                'is_indigenous_group_member', 'indigenous_group_specify',
                'is_pwd', 'pwd_id_image',
                'is_government_member', 'government_level', 'government_role_position',
                'living_arrangement', 'living_arrangement_others_specify',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Remove Student Information Sheet columns from students table
        Schema::table('students', function (Blueprint $table) {
            $columns = [
                'ext_name',
                'street', 'barangay', 'city_municipality', 'province', 'zip_code',
                'nationality',
                'religion', 'tel_no', 'spouse_name', 'spouse_contact_no',
                'sport', 'arts', 'technical',
                'junior_high_school_name', 'junior_high_school_year_completed', 'junior_high_school_address', 'junior_high_school_honors_awards',
                'senior_high_school_name', 'senior_high_school_year_graduated', 'senior_high_school_track_strand', 'senior_high_school_lrn',
                'senior_high_school_address', 'senior_high_school_honors_awards',
                'last_school_attended', 'last_school_course', 'last_school_address', 'last_school_year_attended',
                'father_name', 'father_contact_number', 'father_occupation', 'father_workplace', 'father_monthly_income',
                'mother_name', 'mother_contact_number', 'mother_occupation', 'mother_workplace', 'mother_monthly_income',
                'guardian_name', 'guardian_relationship', 'guardian_contact_number', 'guardian_occupation', 'guardian_workplace', 'guardian_monthly_income',
                'is_active_scholar', 'scholarship_grant_name',
                'is_indigenous_group_member', 'indigenous_group_specify',
                'is_pwd', 'pwd_id_image',
                'is_government_member', 'government_level', 'government_role_position',
                'living_arrangement', 'living_arrangement_others_specify',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
