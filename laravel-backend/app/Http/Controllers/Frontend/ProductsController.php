<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ProductsController extends Controller
{
    public function index(HttpRequest $request)
    {
        try {
            $query = $request->query();
            $internalReq = HttpRequest::create('/api/v1/products', 'GET', $query);
            $response = app()->handle($internalReq);
            $content = $response->getContent();
            $data = $content ? json_decode($content, true) : null;
        } catch (\Exception $e) {
            Log::warning('Frontend ProductsController internal dispatch failed: '.$e->getMessage());
            try {
                $resp = Http::timeout(5)->get(url('/api/v1/products'), $request->query());
                $data = $resp->successful() ? $resp->json() : null;
            } catch (\Exception $e2) {
                Log::warning('Frontend ProductsController external fetch failed: '.$e2->getMessage());
                $data = null;
            }
        }

        return view('products', [
            'productsData' => $data,
            'query' => $query,
        ]);
    }

    public function show(HttpRequest $request, $idOrSlug)
    {
        try {
            $internalReq = HttpRequest::create('/api/v1/products/'.$idOrSlug, 'GET', $request->query());
            $response = app()->handle($internalReq);
            $content = $response->getContent();
            $data = $content ? json_decode($content, true) : null;
        } catch (\Exception $e) {
            Log::warning('Frontend ProductsController show internal dispatch failed: '.$e->getMessage());
            try {
                $resp = Http::timeout(5)->get(url('/api/v1/products/'.$idOrSlug), $request->query());
                $data = $resp->successful() ? $resp->json() : null;
            } catch (\Exception $e2) {
                Log::warning('Frontend ProductsController show external fetch failed: '.$e2->getMessage());
                $data = null;
            }
        }

        return view('product-detail', [
            'productData' => $data,
        ]);
    }
}
