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
        Schema::create('organization_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('file_name'); // Original filename
            $table->string('file_path'); // Storage path
            $table->string('file_type')->nullable(); // e.g., 'personal_data_sheet', 'document', 'image', etc.
            $table->text('description')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // Size in bytes
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->index('organization_id');
            $table->index('uploaded_by');
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_files');
    }
};

