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
        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            if (!$this->hasIndex('users', 'users_email_index')) {
                $table->index('email');
            }
            if (!$this->hasIndex('users', 'users_role_index')) {
                $table->index('role');
            }
            if (!$this->hasIndex('users', 'users_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->hasIndex('users', 'users_department_id_index')) {
                $table->index('department_id');
            }
            if (!$this->hasIndex('users', 'users_course_id_index')) {
                $table->index('course_id');
            }
        });

        // Add indexes to students table
        Schema::table('students', function (Blueprint $table) {
            if (!$this->hasIndex('students', 'students_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->hasIndex('students', 'students_department_id_index')) {
                $table->index('department_id');
            }
            if (!$this->hasIndex('students', 'students_course_id_index')) {
                $table->index('course_id');
            }
            if (!$this->hasIndex('students', 'students_organization_id_index')) {
                $table->index('organization_id');
            }
        });

        // Add indexes to staff table
        Schema::table('staff', function (Blueprint $table) {
            if (!$this->hasIndex('staff', 'staff_email_index')) {
                $table->index('email');
            }
            if (!$this->hasIndex('staff', 'staff_designation_index')) {
                $table->index('designation');
            }
            if (!$this->hasIndex('staff', 'staff_department_id_index')) {
                $table->index('department_id');
            }
        });

        // Add indexes to events table
        Schema::table('events', function (Blueprint $table) {
            if (!$this->hasIndex('events', 'events_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('events', 'events_created_by_index')) {
                $table->index('created_by');
            }
            if (!$this->hasIndex('events', 'events_organization_id_index')) {
                $table->index('organization_id');
            }
            if (!$this->hasIndex('events', 'events_event_date_index')) {
                $table->index('event_date');
            }
        });

        // Add indexes to appointments table
        Schema::table('appointments', function (Blueprint $table) {
            if (!$this->hasIndex('appointments', 'appointments_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->hasIndex('appointments', 'appointments_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('appointments', 'appointments_appointment_date_index')) {
                $table->index('appointment_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['role']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['department_id']);
            $table->dropIndex(['course_id']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['department_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['organization_id']);
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['designation']);
            $table->dropIndex(['department_id']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['organization_id']);
            $table->dropIndex(['event_date']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['appointment_date']);
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = ?
            AND table_name = ?
            AND index_name = ?
        ", [$databaseName, $table, $indexName]);
        
        return $result[0]->count > 0;
    }
};

