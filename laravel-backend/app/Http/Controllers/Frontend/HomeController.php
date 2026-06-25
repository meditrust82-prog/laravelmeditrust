<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(HttpRequest $request)
    {
        try {
            $internalReq = HttpRequest::create('/api/v1/homepage', 'GET', $request->query());
            $response = app()->handle($internalReq);
            $content = $response->getContent();
            $data = $content ? json_decode($content, true) : null;
        } catch (\Exception $e) {
            Log::warning('Frontend HomeController internal dispatch failed: '.$e->getMessage());
            try {
                $resp = Http::timeout(5)->get(url('/api/v1/homepage'));
                $data = $resp->successful() ? $resp->json() : null;
            } catch (\Exception $e2) {
                Log::warning('Frontend HomeController external fetch failed: '.$e2->getMessage());
                $data = null;
            }
        }

        return view('home', [
            'homepage' => $data,
        ]);
    }
}
