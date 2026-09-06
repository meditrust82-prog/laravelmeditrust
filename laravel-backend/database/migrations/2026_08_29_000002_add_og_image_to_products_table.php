<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Dedicated social-share image (Open Graph / Twitter card). Falls back to the product image when null.
            $table->string('og_image')->nullable()->after('og_desc');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('og_image');
        });
    }
};
