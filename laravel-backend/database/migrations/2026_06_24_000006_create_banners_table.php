<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('subtitle', 240)->nullable();
            $table->string('image_url')->nullable();
            $table->string('link_url', 500)->nullable();
            $table->string('link_label', 60)->default('Learn More');
            $table->string('bg_color')->default('#005EEA');
            $table->string('text_color')->default('#ffffff');
            $table->string('placement');
            $table->unsignedInteger('priority')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->index(['placement', 'active', 'priority']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('banners');
    }
};
