<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('students', function (Blueprint $table) {
            // Drop student_id only if it exists
            if (Schema::hasColumn('students', 'student_id')) {
                $table->dropColumn('student_id');
            }
            if (!Schema::hasColumn('students', 'user_id')) {
                $table->unsignedBigInteger('user_id')->unique()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }
    public function down() {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->string('student_id')->unique()->after('id');
        });
    }
};
