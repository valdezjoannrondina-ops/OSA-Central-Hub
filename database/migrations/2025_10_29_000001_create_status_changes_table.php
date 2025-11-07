<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('status_changes', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->unsignedBigInteger('changed_by');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('changed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_changes');
    }
};


