<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'student_id')) {
                $table->dropUnique(['student_id']);
                $table->dropColumn('student_id');
            }
            $table->unsignedBigInteger('user_id')->unique()->nullable()->change();
        });
    }
    public function down() {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_id')->unique()->after('id');
            $table->dropUnique(['user_id'])->nullable();
            $table->unsignedBigInteger('user_id')->change();
        });
    }
};
