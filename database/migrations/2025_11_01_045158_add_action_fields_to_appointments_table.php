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
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('action_taken', ['approve', 'decline', 'reschedule'])->nullable()->after('status');
            $table->text('action_reason')->nullable()->after('action_taken');
            $table->date('rescheduled_date')->nullable()->after('action_reason');
            $table->time('rescheduled_time')->nullable()->after('rescheduled_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['action_taken', 'action_reason', 'rescheduled_date', 'rescheduled_time']);
        });
    }
};
