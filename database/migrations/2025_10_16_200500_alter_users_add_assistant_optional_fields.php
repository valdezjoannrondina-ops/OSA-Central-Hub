<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'image')) {
                $table->string('image')->nullable()->after('organization_id');
            }
            if (!Schema::hasColumn('users', 'service_order')) {
                $table->string('service_order')->nullable()->after('image');
            }
            if (!Schema::hasColumn('users', 'length_of_service')) {
                $table->integer('length_of_service')->nullable()->after('service_order');
            }
            if (!Schema::hasColumn('users', 'contract_end_at')) {
                $table->date('contract_end_at')->nullable()->after('length_of_service');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'contract_end_at')) {
                $table->dropColumn('contract_end_at');
            }
            if (Schema::hasColumn('users', 'length_of_service')) {
                $table->dropColumn('length_of_service');
            }
            if (Schema::hasColumn('users', 'service_order')) {
                $table->dropColumn('service_order');
            }
            if (Schema::hasColumn('users', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('users', 'position')) {
                $table->dropColumn('position');
            }
        });
    }
};
