<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('items');
            $table->decimal('total_price',12,2);
            $table->string('status')->default('pending');
            $table->string('payment_gateway')->nullable();
            $table->string('payment_id')->nullable();
            $table->decimal('payment_amount',12,2)->nullable();
            $table->timestamp('payment_paid_at')->nullable();
            $table->timestamp('payment_expires_at')->nullable();
            $table->string('payment_webhook_event_id')->nullable()->index();
            $table->json('shipping_address')->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
