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
        Schema::table('org_structure_configs', function (Blueprint $table) {
            // Add staff_selections column to store which staff are in each level
            // Format: JSON array where each element is an array of staff IDs for that level
            // Example: [[1, 2], [3, 4, 5], [6, 7, 8, 9]] means level 1 has staff 1,2; level 2 has 3,4,5; etc.
            $table->json('staff_selections')->nullable()->after('staff_per_row');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('org_structure_configs', function (Blueprint $table) {
            $table->dropColumn('staff_selections');
        });
    }
};
