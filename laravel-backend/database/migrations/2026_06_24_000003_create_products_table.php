<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('original_price', 12, 2)->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('category');
            $table->json('images')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('featured')->default(false);
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->json('badges')->nullable();
            $table->timestamps();
            $table->index('category');
            $table->index('featured');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
