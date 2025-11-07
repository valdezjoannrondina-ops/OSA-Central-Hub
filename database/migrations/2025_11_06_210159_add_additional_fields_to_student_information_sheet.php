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
        // Add additional Student Information Sheet columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_single_parent')) {
                $table->boolean('is_single_parent')->default(false)->nullable()->after('living_arrangement_others_specify');
            }
            if (!Schema::hasColumn('users', 'fraternity_sorority_name')) {
                $table->text('fraternity_sorority_name')->nullable()->after('is_single_parent');
            }
            if (!Schema::hasColumn('users', 'fraternity_sorority_position')) {
                $table->text('fraternity_sorority_position')->nullable()->after('fraternity_sorority_name');
            }
            if (!Schema::hasColumn('users', 'has_criminal_record')) {
                $table->boolean('has_criminal_record')->default(false)->nullable()->after('fraternity_sorority_position');
            }
        });

        // Add additional Student Information Sheet columns to students table
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'is_single_parent')) {
                $table->boolean('is_single_parent')->default(false)->nullable()->after('living_arrangement_others_specify');
            }
            if (!Schema::hasColumn('students', 'fraternity_sorority_name')) {
                $table->text('fraternity_sorority_name')->nullable()->after('is_single_parent');
            }
            if (!Schema::hasColumn('students', 'fraternity_sorority_position')) {
                $table->text('fraternity_sorority_position')->nullable()->after('fraternity_sorority_name');
            }
            if (!Schema::hasColumn('students', 'has_criminal_record')) {
                $table->boolean('has_criminal_record')->default(false)->nullable()->after('fraternity_sorority_position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove additional Student Information Sheet columns from users table
        Schema::table('users', function (Blueprint $table) {
            $columns = ['is_single_parent', 'fraternity_sorority_name', 'fraternity_sorority_position', 'has_criminal_record'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Remove additional Student Information Sheet columns from students table
        Schema::table('students', function (Blueprint $table) {
            $columns = ['is_single_parent', 'fraternity_sorority_name', 'fraternity_sorority_position', 'has_criminal_record'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
