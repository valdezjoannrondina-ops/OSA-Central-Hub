<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Requires doctrine/dbal for change()
            if (Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable(false)->change();
            }
            if (Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable(false)->change();
            }
        });
    }
};
