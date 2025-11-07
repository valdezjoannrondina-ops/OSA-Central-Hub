<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('created_by')->constrained('organizations');
            }
        });
    }
    public function down() {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'organization_id')) {
                $table->dropForeign(['organization_id']);
                $table->dropColumn('organization_id');
            }
        });
    }
};
