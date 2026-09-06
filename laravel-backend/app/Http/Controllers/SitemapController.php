<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Blog;

class SitemapController extends Controller
{
    public function index()
    {
        $site = env('APP_URL') ?: 'https://meditrustnepal.com';
        $today = now()->toDateString();
        $products = Product::select('slug', 'updated_at')->get();
        $blogs = Blog::where('published', true)->select('slug', 'updated_at')->get();

        $urls = [];
        $static = [
            ['url' => '/', 'priority' => '1.0', 'freq' => 'weekly'],
            ['url' => '/products', 'priority' => '0.9', 'freq' => 'daily'],
            ['url' => '/about', 'priority' => '0.7', 'freq' => 'monthly'],
            ['url' => '/contact', 'priority' => '0.7', 'freq' => 'monthly'],
            ['url' => '/services', 'priority' => '0.7', 'freq' => 'monthly'],
            ['url' => '/blog', 'priority' => '0.8', 'freq' => 'weekly'],
            ['url' => '/my-quotes', 'priority' => '0.4', 'freq' => 'monthly'],
        ];
        foreach ($static as $page) {
            $urls[] = "<url><loc>{$site}{$page['url']}</loc><lastmod>{$today}</lastmod><changefreq>{$page['freq']}</changefreq><priority>{$page['priority']}</priority></url>";
        }
        foreach ($products as $product) {
            if (!$product->slug) continue;
            $updatedAt = $product->getRawOriginal('updated_at') ? \Illuminate\Support\Carbon::parse($product->getRawOriginal('updated_at')) : null;
            $lastmod = optional($updatedAt)?->toDateString() ?: $today;
            $urls[] = "<url><loc>{$site}/products/{$product->slug}</loc><lastmod>{$lastmod}</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>";
        }
        foreach ($blogs as $blog) {
            if (!$blog->slug) continue;
            $updatedAt = $blog->getRawOriginal('updated_at') ? \Illuminate\Support\Carbon::parse($blog->getRawOriginal('updated_at')) : null;
            $lastmod = optional($updatedAt)?->toDateString() ?: $today;
            $urls[] = "<url><loc>{$site}/blog/{$blog->slug}</loc><lastmod>{$lastmod}</lastmod><changefreq>weekly</changefreq><priority>0.7</priority></url>";
        }

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n" . implode("\n", $urls) . "\n</urlset>";

        return response($xml, 200)->header('Content-Type', 'application/xml')->header('Cache-Control', 'public, max-age=3600');
    }

    public function robots()
    {
        $site = env('APP_URL') ?: 'https://meditrustnepal.com';
        return response("User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /api/\nDisallow: /cart\nDisallow: /compare\n\nSitemap: {$site}/sitemap.xml\n\nUser-agent: GPTBot\nAllow: /\n\nUser-agent: anthropic-ai\nAllow: /\n\nUser-agent: PerplexityBot\nAllow: /", 200)->header('Content-Type', 'text/plain');
    }
}
