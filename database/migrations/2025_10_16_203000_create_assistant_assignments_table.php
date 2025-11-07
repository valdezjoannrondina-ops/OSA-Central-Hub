<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistant_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('position')->nullable();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Optional: prevent duplicate assignment to same org and position
            $table->unique(['user_id','organization_id','position'], 'assistant_assign_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistant_assignments');
    }
};
