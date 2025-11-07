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
        Schema::create('org_structure_configs', function (Blueprint $table) {
            $table->id();
            $table->string('config_key')->unique(); // e.g., 'admin_staff_structure'
            $table->json('staff_per_row'); // Array of numbers: [1, 2, 4, 8, 8] for each level
            $table->integer('max_levels')->default(5); // Maximum number of levels
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('org_structure_configs');
    }
};
