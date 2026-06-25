<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('image')->nullable();
            $table->string('author')->default('Meditrust Nepal');
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('published')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_desc')->nullable();
            $table->timestamps();
            $table->index(['published', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('blogs');
    }
};
