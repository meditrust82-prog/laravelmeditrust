<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServicesSettings;

class ServicesSettingsController extends Controller
{
    protected function singleton(): ServicesSettings
    {
        return ServicesSettings::firstOrCreate(['id' => 'singleton']);
    }

    public function index()
    {
        return response()->json($this->singleton());
    }

    public function update(Request $req)
    {
        $data = $req->only(['heroLabel','heroTitle','heroDesc','services']);
        $doc = $this->singleton();
        if (array_key_exists('heroLabel', $data)) $doc->hero_label = $data['heroLabel'];
        if (array_key_exists('heroTitle', $data)) $doc->hero_title = $data['heroTitle'];
        if (array_key_exists('heroDesc', $data)) $doc->hero_desc = $data['heroDesc'];
        if (array_key_exists('services', $data)) $doc->services = $data['services'];
        $doc->save();
        return response()->json($doc);
    }
}
