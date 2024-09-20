<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('challenge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id');
            $table->foreignId('user_id');
            $table->dateTime('joined_at')->default(now());
            $table->unsignedInteger('total_count');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_user');
    }
};
