<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('event_date')->nullable()->after('id');
        });
    }
    public function down() {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_date');
        });
    }
};
