<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class Blog extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = [
        // basic
        'title', 'slug', 'excerpt', 'content', 'category', 'tags', 'published',
        // media
        'image', 'alt_text', 'caption', 'social_image',
        // seo
        'meta_title', 'meta_desc', 'focus_keyword', 'secondary_keywords', 'search_intent',
        'canonical', 'robots', 'og_title', 'og_desc',
        // aeo
        'primary_question', 'direct_answer', 'key_takeaways', 'faqs',
        // geo
        'country', 'locations', 'entities', 'target_audience',
        // author / reviewer
        'author', 'author_bio', 'author_photo', 'author_credentials', 'author_url',
        'reviewer_name', 'reviewer_designation', 'reviewer_credentials', 'reviewer_url', 'reviewed_at',
        // sources + related
        'sources', 'related_blogs', 'related_products',
        // publishing
        'scheduled_at', 'published_at', 'word_count', 'reading_time',
    ];

    protected $casts = [
        'tags' => 'array',
        'secondary_keywords' => 'array',
        'key_takeaways' => 'array',
        'faqs' => 'array',
        'locations' => 'array',
        'entities' => 'array',
        'target_audience' => 'array',
        'sources' => 'array',
        'related_blogs' => 'array',
        'related_products' => 'array',
        'published' => 'boolean',
        'reviewed_at' => 'date',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'word_count' => 'integer',
        'reading_time' => 'integer',
    ];

    protected $appends = [
        '_id', 'metaTitle', 'metaDesc', 'createdAt', 'updatedAt',
        'altText', 'caption', 'socialImage',
        'focusKeyword', 'secondaryKeywords', 'searchIntent', 'ogTitle', 'ogDesc',
        'primaryQuestion', 'directAnswer', 'keyTakeaways',
        'targetAudience', 'authorBio', 'authorPhoto', 'authorCredentials', 'authorUrl',
        'reviewerName', 'reviewerDesignation', 'reviewerCredentials', 'reviewerUrl', 'reviewedAt',
        'relatedBlogs', 'relatedProducts',
        'scheduledAt', 'publishedAt', 'wordCount', 'readingTime', 'status',
    ];

    // ── Existing scalar accessors ────────────────────────────────
    public function getMetaTitleAttribute($value) { return $this->frontendAttribute('meta_title', $value); }
    public function getMetaDescAttribute($value) { return $this->frontendAttribute('meta_desc', $value); }
    public function getCreatedAtAttribute($value) { return $this->frontendDateAttribute('created_at'); }
    public function getUpdatedAtAttribute($value) { return $this->frontendDateAttribute('updated_at'); }

    // ── Media ────────────────────────────────────────────────────
    public function getAltTextAttribute($value) { return $this->frontendAttribute('alt_text', $value); }
    public function getCaptionAttribute($value) { return $this->frontendAttribute('caption', $value); }
    public function getSocialImageAttribute($value) { return $this->frontendAttribute('social_image', $value); }

    // ── SEO ──────────────────────────────────────────────────────
    public function getFocusKeywordAttribute($value) { return $this->frontendAttribute('focus_keyword', $value); }
    public function getSecondaryKeywordsAttribute() { return $this->frontendJsonAttribute('secondary_keywords'); }
    public function getSearchIntentAttribute($value) { return $this->frontendAttribute('search_intent', $value); }
    public function getOgTitleAttribute($value) { return $this->frontendAttribute('og_title', $value); }
    public function getOgDescAttribute($value) { return $this->frontendAttribute('og_desc', $value); }

    // ── AEO ──────────────────────────────────────────────────────
    public function getPrimaryQuestionAttribute($value) { return $this->frontendAttribute('primary_question', $value); }
    public function getDirectAnswerAttribute($value) { return $this->frontendAttribute('direct_answer', $value); }
    public function getKeyTakeawaysAttribute() { return $this->frontendJsonAttribute('key_takeaways'); }

    // ── GEO ──────────────────────────────────────────────────────
    public function getTargetAudienceAttribute() { return $this->frontendJsonAttribute('target_audience'); }

    // ── Author / reviewer ────────────────────────────────────────
    public function getAuthorBioAttribute($value) { return $this->frontendAttribute('author_bio', $value); }
    public function getAuthorPhotoAttribute($value) { return $this->frontendAttribute('author_photo', $value); }
    public function getAuthorCredentialsAttribute($value) { return $this->frontendAttribute('author_credentials', $value); }
    public function getAuthorUrlAttribute($value) { return $this->frontendAttribute('author_url', $value); }
    public function getReviewerNameAttribute($value) { return $this->frontendAttribute('reviewer_name', $value); }
    public function getReviewerDesignationAttribute($value) { return $this->frontendAttribute('reviewer_designation', $value); }
    public function getReviewerCredentialsAttribute($value) { return $this->frontendAttribute('reviewer_credentials', $value); }
    public function getReviewerUrlAttribute($value) { return $this->frontendAttribute('reviewer_url', $value); }
    public function getReviewedAtAttribute($value)
    {
        $raw = $this->attributes['reviewed_at'] ?? $value ?? null;
        return $raw ? $this->asDate($raw)->toDateString() : null;
    }

    // ── Related ──────────────────────────────────────────────────
    public function getRelatedBlogsAttribute() { return $this->frontendJsonAttribute('related_blogs'); }
    public function getRelatedProductsAttribute() { return $this->frontendJsonAttribute('related_products'); }

    // ── Publishing ───────────────────────────────────────────────
    public function getScheduledAtAttribute($value) { return $this->frontendDateAttribute('scheduled_at'); }
    public function getPublishedAtAttribute($value) { return $this->frontendDateAttribute('published_at'); }
    public function getWordCountAttribute($value) { return (int) $this->frontendAttribute('word_count', $value ?? 0); }
    public function getReadingTimeAttribute($value) { return (int) $this->frontendAttribute('reading_time', $value ?? 0); }

    public function getStatusAttribute(): string
    {
        $scheduled = $this->attributes['scheduled_at'] ?? null;
        $published = (bool) ($this->attributes['published'] ?? false);

        if ($scheduled && $this->asDateTime($scheduled)->isFuture()) {
            return 'scheduled';
        }

        return $published ? 'published' : 'draft';
    }
}
