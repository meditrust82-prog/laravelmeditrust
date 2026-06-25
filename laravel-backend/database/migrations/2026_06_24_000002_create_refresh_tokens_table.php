<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('token')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->string('replaced_by')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('user_agent', 300)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
