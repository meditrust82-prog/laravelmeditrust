<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('organization')->nullable();
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('photo_url')->nullable();
            $table->string('source')->default('direct');
            $table->boolean('visible')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->index(['visible', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('testimonials');
    }
};
