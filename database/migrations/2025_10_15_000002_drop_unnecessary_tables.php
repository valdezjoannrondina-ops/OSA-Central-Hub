<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Disabled: This migration was attempting to drop core tables (users, departments, etc.)
        // which caused foreign key constraint failures and data loss risk.
        // Intentionally left blank to preserve schema.
        return; // no-op
    }

    public function down(): void
    {
        // No-op
    }
};
