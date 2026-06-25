<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    public function index(Request $request)
    {
        try {
            $internalReq = Request::create('/api/v1/about-settings', 'GET', $request->query());
            $response = app()->handle($internalReq);
            $content = $response->getContent();
            $data = $content ? json_decode($content, true) : null;
        } catch (\Exception $e) {
            Log::warning('AboutController internal dispatch failed: '.$e->getMessage());
            $data = null;
        }

        return view('about', [
            'aboutData' => $data,
        ]);
    }
}
