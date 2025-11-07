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
        Schema::table('organization_files', function (Blueprint $table) {
            if (!Schema::hasColumn('organization_files', 'file_category')) {
                $table->string('file_category')->nullable()->after('file_type');
                $table->index('file_category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_files', function (Blueprint $table) {
            if (Schema::hasColumn('organization_files', 'file_category')) {
                $table->dropColumn('file_category');
            }
        });
    }
};

