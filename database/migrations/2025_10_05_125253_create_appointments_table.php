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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // null = guest
            $table->string('full_name')->nullable(); // for guests
            $table->string('email')->nullable();     // for guests
            $table->string('contact_number')->nullable();
            $table->date('appointment_date');
            $table->time('appointment_time')->nullable();
            $table->string('concern');
            $table->text('message')->nullable();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'cancelled', 'rescheduled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
