<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            if (!Schema::hasColumn('staff', 'user_id')) {
                $table->string('user_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('staff', 'service_order')) {
                $table->string('service_order')->nullable()->after('image');
            }
            if (!Schema::hasColumn('staff', 'length_of_service')) {
                $table->unsignedInteger('length_of_service')->nullable()->after('age');
            }
            if (!Schema::hasColumn('staff', 'contract_end_at')) {
                $table->dateTime('contract_end_at')->nullable()->after('length_of_service');
            }
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
            if (Schema::hasColumn('staff', 'length_of_service')) {
                $table->dropColumn('length_of_service');
            }
            if (Schema::hasColumn('staff', 'service_order')) {
                $table->dropColumn('service_order');
            }
            if (Schema::hasColumn('staff', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
