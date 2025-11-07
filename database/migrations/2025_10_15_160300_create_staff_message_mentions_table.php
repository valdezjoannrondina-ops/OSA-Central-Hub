<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff_message_mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('staff_messages')->onDelete('cascade');
            $table->foreignId('mentioned_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['message_id', 'mentioned_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_message_mentions');
    }
};
