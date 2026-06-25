<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_slug')->nullable();
            $table->string('name');
            $table->string('hospital_name')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->unsignedInteger('qty')->default(1);
            $table->string('source')->default('product_detail');
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotes');
    }
};
