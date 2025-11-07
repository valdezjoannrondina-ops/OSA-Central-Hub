<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('staff_messages')) {
            Schema::table('staff_messages', function (Blueprint $table) {
                $table->foreignId('parent_id')->nullable()->after('user_id')
                    ->constrained('staff_messages')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('staff_messages')) {
            Schema::table('staff_messages', function (Blueprint $table) {
                if (Schema::hasColumn('staff_messages', 'parent_id')) {
                    $table->dropForeign(['parent_id']);
                    $table->dropColumn('parent_id');
                }
            });
        }
    }
};
