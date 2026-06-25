<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class AboutSettings extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $table = 'about_settings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'hero_label', 'hero_title', 'hero_desc', 'story_para1', 'story_para2',
        'story_badge', 'mission_text', 'vision_text', 'values', 'team', 'milestones',
    ];

    protected $casts = [
        'values' => 'array',
        'team' => 'array',
        'milestones' => 'array',
    ];

    protected $appends = ['_id', 'heroLabel', 'heroTitle', 'heroDesc', 'storyPara1', 'storyPara2', 'storyBadge', 'missionText', 'visionText'];

    public function getHeroLabelAttribute($value) { return $this->frontendAttribute('hero_label', $value); }
    public function getHeroTitleAttribute($value) { return $this->frontendAttribute('hero_title', $value); }
    public function getHeroDescAttribute($value) { return $this->frontendAttribute('hero_desc', $value); }
    public function getStoryPara1Attribute($value) { return $this->frontendAttribute('story_para1', $value); }
    public function getStoryPara2Attribute($value) { return $this->frontendAttribute('story_para2', $value); }
    public function getStoryBadgeAttribute($value) { return $this->frontendAttribute('story_badge', $value); }
    public function getMissionTextAttribute($value) { return $this->frontendAttribute('mission_text', $value); }
    public function getVisionTextAttribute($value) { return $this->frontendAttribute('vision_text', $value); }
}
