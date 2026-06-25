<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AboutSettings;

class AboutController extends Controller
{
    protected function singleton(): AboutSettings
    {
        return AboutSettings::firstOrCreate(['id' => 'singleton']);
    }

    public function index()
    {
        return response()->json($this->singleton());
    }

    public function update(Request $req)
    {
        $data = $req->only(['heroLabel','heroTitle','heroDesc','storyPara1','storyPara2','storyBadge','missionText','visionText','values','team','milestones']);
        $doc = $this->singleton();
        if (array_key_exists('heroLabel', $data)) $doc->hero_label = $data['heroLabel'];
        if (array_key_exists('heroTitle', $data)) $doc->hero_title = $data['heroTitle'];
        if (array_key_exists('heroDesc', $data)) $doc->hero_desc = $data['heroDesc'];
        if (array_key_exists('storyPara1', $data)) $doc->story_para1 = $data['storyPara1'];
        if (array_key_exists('storyPara2', $data)) $doc->story_para2 = $data['storyPara2'];
        if (array_key_exists('storyBadge', $data)) $doc->story_badge = $data['storyBadge'];
        if (array_key_exists('missionText', $data)) $doc->mission_text = $data['missionText'];
        if (array_key_exists('visionText', $data)) $doc->vision_text = $data['visionText'];
        if (array_key_exists('values', $data)) $doc->values = $data['values'];
        if (array_key_exists('team', $data)) $doc->team = $data['team'];
        if (array_key_exists('milestones', $data)) $doc->milestones = $data['milestones'];
        $doc->save();
        return response()->json($doc);
    }
}
