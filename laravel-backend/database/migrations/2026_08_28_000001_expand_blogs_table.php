<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('blogs', function (Blueprint $table) {
            // ── Media ───────────────────────────────────────────
            if (!Schema::hasColumn('blogs', 'alt_text')) {
                $table->string('alt_text')->nullable()->after('image');
            }
            if (!Schema::hasColumn('blogs', 'caption')) {
                $table->string('caption')->nullable()->after('alt_text');
            }
            if (!Schema::hasColumn('blogs', 'social_image')) {
                $table->string('social_image')->nullable()->after('caption');
            }

            // ── SEO ─────────────────────────────────────────────
            if (!Schema::hasColumn('blogs', 'focus_keyword')) {
                $table->string('focus_keyword')->nullable()->after('meta_desc');
            }
            if (!Schema::hasColumn('blogs', 'secondary_keywords')) {
                $table->json('secondary_keywords')->nullable()->after('focus_keyword');
            }
            if (!Schema::hasColumn('blogs', 'search_intent')) {
                $table->string('search_intent')->nullable()->after('secondary_keywords');
            }
            if (!Schema::hasColumn('blogs', 'canonical')) {
                $table->string('canonical')->nullable()->after('search_intent');
            }
            if (!Schema::hasColumn('blogs', 'robots')) {
                $table->string('robots')->nullable()->default('index,follow')->after('canonical');
            }
            if (!Schema::hasColumn('blogs', 'og_title')) {
                $table->string('og_title')->nullable()->after('robots');
            }
            if (!Schema::hasColumn('blogs', 'og_desc')) {
                $table->string('og_desc')->nullable()->after('og_title');
            }

            // ── AEO ─────────────────────────────────────────────
            if (!Schema::hasColumn('blogs', 'primary_question')) {
                $table->string('primary_question')->nullable()->after('og_desc');
            }
            if (!Schema::hasColumn('blogs', 'direct_answer')) {
                $table->text('direct_answer')->nullable()->after('primary_question');
            }
            if (!Schema::hasColumn('blogs', 'key_takeaways')) {
                $table->json('key_takeaways')->nullable()->after('direct_answer');
            }
            if (!Schema::hasColumn('blogs', 'faqs')) {
                $table->json('faqs')->nullable()->after('key_takeaways');
            }

            // ── GEO ─────────────────────────────────────────────
            if (!Schema::hasColumn('blogs', 'country')) {
                $table->string('country')->nullable()->default('Nepal')->after('faqs');
            }
            if (!Schema::hasColumn('blogs', 'locations')) {
                $table->json('locations')->nullable()->after('country');
            }
            if (!Schema::hasColumn('blogs', 'entities')) {
                $table->json('entities')->nullable()->after('locations');
            }
            if (!Schema::hasColumn('blogs', 'target_audience')) {
                $table->json('target_audience')->nullable()->after('entities');
            }

            // ── E-E-A-T / Author + Reviewer ─────────────────────
            if (!Schema::hasColumn('blogs', 'author_bio')) {
                $table->text('author_bio')->nullable()->after('target_audience');
            }
            if (!Schema::hasColumn('blogs', 'author_photo')) {
                $table->string('author_photo')->nullable()->after('author_bio');
            }
            if (!Schema::hasColumn('blogs', 'author_credentials')) {
                $table->string('author_credentials')->nullable()->after('author_photo');
            }
            if (!Schema::hasColumn('blogs', 'author_url')) {
                $table->string('author_url')->nullable()->after('author_credentials');
            }
            if (!Schema::hasColumn('blogs', 'reviewer_name')) {
                $table->string('reviewer_name')->nullable()->after('author_url');
            }
            if (!Schema::hasColumn('blogs', 'reviewer_designation')) {
                $table->string('reviewer_designation')->nullable()->after('reviewer_name');
            }
            if (!Schema::hasColumn('blogs', 'reviewer_credentials')) {
                $table->string('reviewer_credentials')->nullable()->after('reviewer_designation');
            }
            if (!Schema::hasColumn('blogs', 'reviewer_url')) {
                $table->string('reviewer_url')->nullable()->after('reviewer_credentials');
            }
            if (!Schema::hasColumn('blogs', 'reviewed_at')) {
                $table->date('reviewed_at')->nullable()->after('reviewer_url');
            }

            // ── Sources ─────────────────────────────────────────
            if (!Schema::hasColumn('blogs', 'sources')) {
                $table->json('sources')->nullable()->after('reviewed_at');
            }

            // ── Related content (internal linking) ──────────────
            if (!Schema::hasColumn('blogs', 'related_blogs')) {
                $table->json('related_blogs')->nullable()->after('sources');
            }
            if (!Schema::hasColumn('blogs', 'related_products')) {
                $table->json('related_products')->nullable()->after('related_blogs');
            }

            // ── Publishing ──────────────────────────────────────
            if (!Schema::hasColumn('blogs', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('published');
            }
            if (!Schema::hasColumn('blogs', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('scheduled_at');
            }
            if (!Schema::hasColumn('blogs', 'word_count')) {
                $table->unsignedInteger('word_count')->default(0)->after('published_at');
            }
            if (!Schema::hasColumn('blogs', 'reading_time')) {
                $table->unsignedInteger('reading_time')->default(0)->after('word_count');
            }
        });
    }

    public function down()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $columns = [
                'alt_text', 'caption', 'social_image',
                'focus_keyword', 'secondary_keywords', 'search_intent', 'canonical', 'robots', 'og_title', 'og_desc',
                'primary_question', 'direct_answer', 'key_takeaways', 'faqs',
                'country', 'locations', 'entities', 'target_audience',
                'author_bio', 'author_photo', 'author_credentials', 'author_url',
                'reviewer_name', 'reviewer_designation', 'reviewer_credentials', 'reviewer_url', 'reviewed_at',
                'sources', 'related_blogs', 'related_products',
                'scheduled_at', 'published_at', 'word_count', 'reading_time',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('blogs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
