<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('scholarship_id')->nullable();
            $table->integer('year_level')->nullable();
            $table->enum('student_type1', ['regular', 'irregular', 'transferee'])->nullable();
            $table->enum('student_type2', ['paying', 'scholar'])->nullable();
            $table->string('contact_number')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('students');
    }
};
