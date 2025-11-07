<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Change length_of_service to VARCHAR(100) to store human-readable LOS
        if (Schema::hasColumn('staff', 'length_of_service')) {
            DB::statement("ALTER TABLE `staff` MODIFY `length_of_service` VARCHAR(100) NULL");
        } else {
            Schema::table('staff', function (Blueprint $table) {
                $table->string('length_of_service', 100)->nullable()->after('age');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('staff', 'length_of_service')) {
            // Revert to unsignedInteger if needed
            DB::statement("ALTER TABLE `staff` MODIFY `length_of_service` INT UNSIGNED NULL");
        }
    }
};
