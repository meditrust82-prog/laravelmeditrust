<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class Product extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = [
        'name', 'slug', 'description', 'specifications', 'brand', 'price',
        'original_price', 'cost', 'category', 'images', 'stock', 'featured',
        'meta_title', 'meta_description', 'meta_keywords', 'badges',
        // SEO
        'focus_keyword', 'canonical', 'robots', 'og_title', 'og_desc', 'og_image',
        // AEO
        'primary_question', 'direct_answer', 'key_takeaways', 'faqs',
        // GEO
        'country', 'locations', 'entities', 'target_audience', 'search_intent',
    ];

    protected $casts = [
        'images' => 'array',
        'badges' => 'array',
        'key_takeaways' => 'array',
        'faqs' => 'array',
        'locations' => 'array',
        'entities' => 'array',
        'target_audience' => 'array',
        'featured' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected $appends = ['_id', 'image', 'allImages', 'categorySlug', 'quantity', 'originalPrice', 'metaTitle', 'metaDescription', 'metaKeywords', 'focusKeyword', 'canonical', 'robots', 'ogTitle', 'ogDesc', 'ogImage', 'primaryQuestion', 'directAnswer', 'keyTakeaways', 'faqs', 'country', 'locations', 'entities', 'targetAudience', 'searchIntent', 'createdAt', 'updatedAt'];

    public function getImageAttribute()
    {
        return $this->images[0]['url'] ?? $this->images[0]['path'] ?? null;
    }

    public function getAllImagesAttribute()
    {
        return collect($this->images ?? [])
            ->map(fn ($img) => $img['url'] ?? $img['path'] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    public function getCategorySlugAttribute()
    {
        return $this->category ? str($this->category)->slug()->toString() : null;
    }

    public function getQuantityAttribute()
    {
        return $this->stock;
    }

    public function getOriginalPriceAttribute($value)
    {
        return $this->frontendAttribute('original_price', $value);
    }

    public function getMetaTitleAttribute($value)
    {
        return $this->frontendAttribute('meta_title', $value);
    }

    public function getMetaDescriptionAttribute($value)
    {
        return $this->frontendAttribute('meta_description', $value);
    }

    public function getMetaKeywordsAttribute($value)
    {
        return $this->frontendAttribute('meta_keywords', $value);
    }

    // ── SEO ────────────────────────────────────────────────────
    public function getFocusKeywordAttribute($value) { return $this->frontendAttribute('focus_keyword', $value); }
    public function getCanonicalAttribute($value) { return $this->frontendAttribute('canonical', $value); }
    public function getRobotsAttribute($value) { return $this->frontendAttribute('robots', $value ?? 'index,follow'); }
    public function getOgTitleAttribute($value) { return $this->frontendAttribute('og_title', $value); }
    public function getOgDescAttribute($value) { return $this->frontendAttribute('og_desc', $value); }
    public function getOgImageAttribute($value) { return $this->frontendAttribute('og_image', $value); }

    // ── AEO ────────────────────────────────────────────────────
    public function getPrimaryQuestionAttribute($value) { return $this->frontendAttribute('primary_question', $value); }
    public function getDirectAnswerAttribute($value) { return $this->frontendAttribute('direct_answer', $value); }
    public function getKeyTakeawaysAttribute() { return $this->frontendJsonAttribute('key_takeaways'); }
    public function getFaqsAttribute() { return $this->frontendJsonAttribute('faqs'); }

    // ── GEO ────────────────────────────────────────────────────
    public function getCountryAttribute($value) { return $this->frontendAttribute('country', $value ?? 'Nepal'); }
    public function getLocationsAttribute() { return $this->frontendJsonAttribute('locations'); }
    public function getEntitiesAttribute() { return $this->frontendJsonAttribute('entities'); }
    public function getTargetAudienceAttribute() { return $this->frontendJsonAttribute('target_audience'); }
    public function getSearchIntentAttribute($value) { return $this->frontendAttribute('search_intent', $value ?? 'transactional'); }

    public function getCreatedAtAttribute($value)
    {
        return $this->frontendDateAttribute('created_at');
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->frontendDateAttribute('updated_at');
    }
}
