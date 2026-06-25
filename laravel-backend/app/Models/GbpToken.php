<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class GbpToken extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $table = 'gbp_tokens';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'access_token', 'refresh_token', 'expiry_date', 'account_id', 'location_id',
        'account_name', 'location_name', 'cached_reviews', 'cached_business_info',
        'reviews_cached_at', 'business_info_cached_at',
    ];

    protected $casts = [
        'cached_reviews' => 'array',
        'cached_business_info' => 'array',
        'reviews_cached_at' => 'datetime',
        'business_info_cached_at' => 'datetime',
        'expiry_date' => 'integer',
    ];

    protected $appends = ['_id', 'accessToken', 'refreshToken', 'expiryDate', 'accountId', 'locationId', 'accountName', 'locationName', 'cachedReviews', 'cachedBusinessInfo', 'reviewsCachedAt', 'businessInfoCachedAt'];

    public function getAccessTokenAttribute($value) { return $this->frontendAttribute('access_token', $value); }
    public function getRefreshTokenAttribute($value) { return $this->frontendAttribute('refresh_token', $value); }
    public function getExpiryDateAttribute($value) { return $this->frontendAttribute('expiry_date', $value); }
    public function getAccountIdAttribute($value) { return $this->frontendAttribute('account_id', $value); }
    public function getLocationIdAttribute($value) { return $this->frontendAttribute('location_id', $value); }
    public function getAccountNameAttribute($value) { return $this->frontendAttribute('account_name', $value); }
    public function getLocationNameAttribute($value) { return $this->frontendAttribute('location_name', $value); }
    public function getCachedReviewsAttribute($value) { return $this->frontendJsonAttribute('cached_reviews'); }
    public function getCachedBusinessInfoAttribute($value) { return $this->frontendJsonAttribute('cached_business_info'); }
    public function getReviewsCachedAtAttribute($value) { return $this->frontendDateAttribute('reviews_cached_at'); }
    public function getBusinessInfoCachedAtAttribute($value) { return $this->frontendDateAttribute('business_info_cached_at'); }
}
