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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('acronym')->nullable()->after('name');
            $table->text('mailing_address')->nullable()->after('acronym');
            $table->date('date_established')->nullable()->after('mailing_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['acronym', 'mailing_address', 'date_established']);
        });
    }
};
