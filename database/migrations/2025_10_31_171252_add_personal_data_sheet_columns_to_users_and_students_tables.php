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
        // Add Personal Data Sheet columns to users table
        Schema::table('users', function (Blueprint $table) {
            // Section A: Name extensions
            $table->integer('age')->nullable()->after('birth_date');
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('gender');
            
            // Section B: Maiden name
            $table->string('maiden_name')->nullable()->after('civil_status');
            
            // Section C: Birth details
            $table->string('place_of_birth')->nullable()->after('birth_date');
            
            // Section D: Address
            $table->text('complete_home_address')->nullable()->after('contact_number');
            
            // Section E: Parent/Spouse/Guardian
            $table->string('parent_spouse_guardian')->nullable()->after('emergency_relation');
            $table->text('parent_spouse_guardian_address')->nullable()->after('parent_spouse_guardian');
            
            // Section G: Schools Attended
            $table->string('elementary_school')->nullable()->after('parent_spouse_guardian_address');
            $table->string('elementary_address')->nullable()->after('elementary_school');
            $table->string('elementary_year_graduated')->nullable()->after('elementary_address');
            $table->string('high_school')->nullable()->after('elementary_year_graduated');
            $table->string('high_school_address')->nullable()->after('high_school');
            $table->string('high_school_year_graduated')->nullable()->after('high_school_address');
            $table->string('college_name')->nullable()->after('high_school_year_graduated');
            $table->string('college_address')->nullable()->after('college_name');
            $table->string('college_course')->nullable()->after('college_address');
            $table->string('college_year')->nullable()->after('college_course');
            
            // Form header fields
            $table->string('school_year')->nullable()->after('year_level');
            $table->string('semester')->nullable()->after('school_year');
            $table->enum('student_type', ['new', 'old'])->nullable()->after('student_type2');
            
            // Section I: Entrance Credentials
            $table->boolean('form_137_presented')->default(false)->nullable()->after('student_type');
            $table->boolean('tor_presented')->default(false)->nullable()->after('form_137_presented');
            $table->boolean('good_moral_cert_presented')->default(false)->nullable()->after('tor_presented');
            $table->boolean('birth_cert_presented')->default(false)->nullable()->after('good_moral_cert_presented');
            $table->boolean('marriage_cert_presented')->default(false)->nullable()->after('birth_cert_presented');
        });

        // Add Personal Data Sheet columns to students table
        Schema::table('students', function (Blueprint $table) {
            // Section A: Name extensions
            $table->integer('age')->nullable()->after('birth_date');
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('gender');
            
            // Section B: Maiden name
            $table->string('maiden_name')->nullable()->after('civil_status');
            
            // Section C: Birth details
            $table->string('place_of_birth')->nullable()->after('birth_date');
            
            // Section D: Address
            $table->text('complete_home_address')->nullable()->after('contact_number');
            
            // Section E: Parent/Spouse/Guardian
            $table->string('parent_spouse_guardian')->nullable()->after('emergency_relation');
            $table->text('parent_spouse_guardian_address')->nullable()->after('parent_spouse_guardian');
            
            // Section G: Schools Attended
            $table->string('elementary_school')->nullable()->after('parent_spouse_guardian_address');
            $table->string('elementary_address')->nullable()->after('elementary_school');
            $table->string('elementary_year_graduated')->nullable()->after('elementary_address');
            $table->string('high_school')->nullable()->after('elementary_year_graduated');
            $table->string('high_school_address')->nullable()->after('high_school');
            $table->string('high_school_year_graduated')->nullable()->after('high_school_address');
            $table->string('college_name')->nullable()->after('high_school_year_graduated');
            $table->string('college_address')->nullable()->after('college_name');
            $table->string('college_course')->nullable()->after('college_address');
            $table->string('college_year')->nullable()->after('college_course');
            
            // Form header fields
            $table->string('school_year')->nullable()->after('year_level');
            $table->string('semester')->nullable()->after('school_year');
            $table->enum('student_type', ['new', 'old'])->nullable()->after('student_type2');
            
            // Section I: Entrance Credentials
            $table->boolean('form_137_presented')->default(false)->nullable()->after('student_type');
            $table->boolean('tor_presented')->default(false)->nullable()->after('form_137_presented');
            $table->boolean('good_moral_cert_presented')->default(false)->nullable()->after('tor_presented');
            $table->boolean('birth_cert_presented')->default(false)->nullable()->after('good_moral_cert_presented');
            $table->boolean('marriage_cert_presented')->default(false)->nullable()->after('birth_cert_presented');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Personal Data Sheet columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'age',
                'civil_status',
                'maiden_name',
                'place_of_birth',
                'complete_home_address',
                'parent_spouse_guardian',
                'parent_spouse_guardian_address',
                'elementary_school',
                'elementary_address',
                'elementary_year_graduated',
                'high_school',
                'high_school_address',
                'high_school_year_graduated',
                'college_name',
                'college_address',
                'college_course',
                'college_year',
                'school_year',
                'semester',
                'student_type',
                'form_137_presented',
                'tor_presented',
                'good_moral_cert_presented',
                'birth_cert_presented',
                'marriage_cert_presented',
            ]);
        });

        // Remove Personal Data Sheet columns from students table
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'age',
                'civil_status',
                'maiden_name',
                'place_of_birth',
                'complete_home_address',
                'parent_spouse_guardian',
                'parent_spouse_guardian_address',
                'elementary_school',
                'elementary_address',
                'elementary_year_graduated',
                'high_school',
                'high_school_address',
                'high_school_year_graduated',
                'college_name',
                'college_address',
                'college_course',
                'college_year',
                'school_year',
                'semester',
                'student_type',
                'form_137_presented',
                'tor_presented',
                'good_moral_cert_presented',
                'birth_cert_presented',
                'marriage_cert_presented',
            ]);
        });
    }
};
