<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // Add user_id if missing
            if (!Schema::hasColumn('staff', 'user_id')) {
                $table->string('user_id')->nullable()->unique()->after('id');
            }
            // Add contract_end_at if missing
            if (!Schema::hasColumn('staff', 'contract_end_at')) {
                $table->dateTime('contract_end_at')->nullable()->after('age');
            }
            // Add employment_status if missing
            if (!Schema::hasColumn('staff', 'employment_status')) {
                $table->string('employment_status')->default('active')->after('contract_end_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            if (Schema::hasColumn('staff', 'employment_status')) {
                $table->dropColumn('employment_status');
            }
            if (Schema::hasColumn('staff', 'contract_end_at')) {
                $table->dropColumn('contract_end_at');
            }
            if (Schema::hasColumn('staff', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
