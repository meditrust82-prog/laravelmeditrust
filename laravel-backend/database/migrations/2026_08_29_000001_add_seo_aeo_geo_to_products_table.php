<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // ── SEO ──
            $table->string('focus_keyword')->nullable()->after('meta_keywords');
            $table->string('canonical')->nullable()->after('focus_keyword');
            $table->string('robots')->nullable()->default('index,follow')->after('canonical');
            $table->string('og_title')->nullable()->after('robots');
            $table->string('og_desc')->nullable()->after('og_title');

            // ── AEO (Answer Engine Optimization) ──
            $table->string('primary_question')->nullable()->after('og_desc');
            $table->text('direct_answer')->nullable()->after('primary_question');
            $table->json('key_takeaways')->nullable()->after('direct_answer');
            $table->json('faqs')->nullable()->after('key_takeaways');

            // ── GEO (Generative Engine Optimization / local relevance) ──
            $table->string('country')->nullable()->default('Nepal')->after('faqs');
            $table->json('locations')->nullable()->after('country');
            $table->json('entities')->nullable()->after('locations');
            $table->json('target_audience')->nullable()->after('entities');
            $table->string('search_intent')->nullable()->default('transactional')->after('target_audience');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'focus_keyword', 'canonical', 'robots', 'og_title', 'og_desc',
                'primary_question', 'direct_answer', 'key_takeaways', 'faqs',
                'country', 'locations', 'entities', 'target_audience', 'search_intent',
            ]);
        });
    }
};
