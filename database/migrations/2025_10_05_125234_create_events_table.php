<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('qr_code_path');
            $table->foreignId('created_by')->constrained('users');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('events');
    }
};
