<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('hero_badge')->nullable();
            $table->longText('hero_title')->nullable();
            $table->longText('hero_subtitle')->nullable();
            $table->string('hero_primary_btn')->nullable();
            $table->string('hero_secondary_btn')->nullable();
            $table->json('stats')->nullable();
            $table->json('trusted_by')->nullable();
            $table->json('why_choose_us')->nullable();
            $table->timestamps();
        });

        Schema::create('about_settings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('hero_label')->nullable();
            $table->string('hero_title')->nullable();
            $table->longText('hero_desc')->nullable();
            $table->longText('story_para1')->nullable();
            $table->longText('story_para2')->nullable();
            $table->string('story_badge')->nullable();
            $table->longText('mission_text')->nullable();
            $table->longText('vision_text')->nullable();
            $table->json('values')->nullable();
            $table->json('team')->nullable();
            $table->json('milestones')->nullable();
            $table->timestamps();
        });

        Schema::create('legal_settings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('privacy_title')->nullable();
            $table->string('privacy_last_updated')->nullable();
            $table->longText('privacy_content')->nullable();
            $table->string('terms_title')->nullable();
            $table->string('terms_last_updated')->nullable();
            $table->longText('terms_content')->nullable();
            $table->timestamps();
        });

        Schema::create('services_settings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('hero_label')->nullable();
            $table->string('hero_title')->nullable();
            $table->longText('hero_desc')->nullable();
            $table->json('services')->nullable();
            $table->timestamps();
        });

        Schema::create('gbp_tokens', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->unsignedBigInteger('expiry_date')->nullable();
            $table->string('account_id')->nullable();
            $table->string('location_id')->nullable();
            $table->string('account_name')->nullable();
            $table->string('location_name')->nullable();
            $table->json('cached_reviews')->nullable();
            $table->json('cached_business_info')->nullable();
            $table->timestamp('reviews_cached_at')->nullable();
            $table->timestamp('business_info_cached_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('homepage_settings');
        Schema::dropIfExists('about_settings');
        Schema::dropIfExists('legal_settings');
        Schema::dropIfExists('services_settings');
        Schema::dropIfExists('gbp_tokens');
    }
};
