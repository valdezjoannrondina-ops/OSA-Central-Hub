<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('event_id')->constrained('events');
            $table->dateTime('scan_time');
            $table->enum('status', ['Present', 'Late', 'Absent']);
            $table->string('excuse_letter')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('attendances');
    }
};
