<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class LegalSettings extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $table = 'legal_settings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'privacy_title', 'privacy_last_updated', 'privacy_content',
        'terms_title', 'terms_last_updated', 'terms_content',
    ];

    protected $appends = ['_id', 'privacyTitle', 'privacyLastUpdated', 'privacyContent', 'termsTitle', 'termsLastUpdated', 'termsContent'];

    public function getPrivacyTitleAttribute($value) { return $this->frontendAttribute('privacy_title', $value); }
    public function getPrivacyLastUpdatedAttribute($value) { return $this->frontendAttribute('privacy_last_updated', $value); }
    public function getPrivacyContentAttribute($value) { return $this->frontendAttribute('privacy_content', $value); }
    public function getTermsTitleAttribute($value) { return $this->frontendAttribute('terms_title', $value); }
    public function getTermsLastUpdatedAttribute($value) { return $this->frontendAttribute('terms_last_updated', $value); }
    public function getTermsContentAttribute($value) { return $this->frontendAttribute('terms_content', $value); }
}
