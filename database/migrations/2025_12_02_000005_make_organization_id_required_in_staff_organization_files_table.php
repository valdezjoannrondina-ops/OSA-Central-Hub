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
        // First, delete any existing files without organization
        \Illuminate\Support\Facades\DB::table('staff_organization_files')
            ->whereNull('organization_id')
            ->delete(); // Delete files without organization
        
        Schema::table('staff_organization_files', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['organization_id']);
        });
        
        // Make the column NOT NULL
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE staff_organization_files MODIFY COLUMN organization_id BIGINT UNSIGNED NOT NULL');
        
        // Recreate the foreign key constraint with CASCADE instead of SET NULL
        Schema::table('staff_organization_files', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_organization_files', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['organization_id']);
        });
        
        // Revert back to nullable
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE staff_organization_files MODIFY COLUMN organization_id BIGINT UNSIGNED NULL');
        
        // Recreate the foreign key constraint with SET NULL
        Schema::table('staff_organization_files', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
        });
    }
};

