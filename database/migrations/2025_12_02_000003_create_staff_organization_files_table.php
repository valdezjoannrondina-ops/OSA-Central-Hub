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
        Schema::create('staff_organization_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
            $table->string('file_name'); // Original filename
            $table->string('file_path'); // Storage path
            $table->string('file_type')->nullable(); // e.g., 'personal_data_sheet', 'document', 'other'
            $table->string('file_category')->nullable(); // e.g., 'Personal Data Sheet', 'Documents', 'Other'
            $table->integer('file_size')->nullable(); // Size in bytes
            $table->string('mime_type')->nullable(); // MIME type
            $table->text('description')->nullable(); // Optional description
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('staff_id');
            $table->index('organization_id');
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_organization_files');
    }
};

