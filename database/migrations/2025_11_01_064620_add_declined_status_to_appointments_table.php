<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to alter the enum to add 'declined'
        // This is a raw SQL operation since Laravel's schema builder doesn't support enum modification
        DB::statement("ALTER TABLE `appointments` MODIFY COLUMN `status` ENUM('pending', 'approved', 'cancelled', 'rescheduled', 'declined') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'declined' from the enum
        DB::statement("ALTER TABLE `appointments` MODIFY COLUMN `status` ENUM('pending', 'approved', 'cancelled', 'rescheduled') DEFAULT 'pending'");
    }
};
