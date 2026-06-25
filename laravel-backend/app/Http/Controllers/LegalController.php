<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LegalSettings;

class LegalController extends Controller
{
    protected function singleton(): LegalSettings
    {
        return LegalSettings::firstOrCreate(['id' => 'singleton'], [
            'privacy_title' => 'Privacy Policy',
            'terms_title' => 'Terms & Conditions',
        ]);
    }

    public function index()
    {
        return response()->json($this->singleton());
    }

    public function update(Request $req)
    {
        $data = $req->only(['privacyTitle','privacyLastUpdated','privacyContent','termsTitle','termsLastUpdated','termsContent']);
        $doc = $this->singleton();
        if (array_key_exists('privacyTitle', $data)) $doc->privacy_title = $data['privacyTitle'];
        if (array_key_exists('privacyLastUpdated', $data)) $doc->privacy_last_updated = $data['privacyLastUpdated'];
        if (array_key_exists('privacyContent', $data)) $doc->privacy_content = $data['privacyContent'];
        if (array_key_exists('termsTitle', $data)) $doc->terms_title = $data['termsTitle'];
        if (array_key_exists('termsLastUpdated', $data)) $doc->terms_last_updated = $data['termsLastUpdated'];
        if (array_key_exists('termsContent', $data)) $doc->terms_content = $data['termsContent'];
        $doc->save();
        return response()->json($doc);
    }
}
