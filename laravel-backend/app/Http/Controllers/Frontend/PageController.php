<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    protected function fetchApi(string $uri, array $query = []): ?array
    {
        try {
            $internalRequest = HttpRequest::create($uri, 'GET', $query);
            $response = app()->handle($internalRequest);
            $content = $response->getContent();
            return $content ? json_decode($content, true) : null;
        } catch (\Throwable $exception) {
            Log::warning('PageController internal API request failed for ' . $uri . ': ' . $exception->getMessage());
            try {
                $response = Http::timeout(5)->get(url($uri), $query);
                return $response->successful() ? $response->json() : null;
            } catch (\Throwable $fallback) {
                Log::warning('PageController fallback HTTP request failed for ' . $uri . ': ' . $fallback->getMessage());
                return null;
            }
        }
    }

    public function home(HttpRequest $request)
    {
        $data = $this->fetchApi('/api/v1/homepage', $request->query());
        return view('home', ['homepage' => $data]);
    }

    public function services(HttpRequest $request)
    {
        $data = $this->fetchApi('/api/v1/services-settings', $request->query());
        return view('services', ['servicesSettings' => $data]);
    }

    public function about(HttpRequest $request)
    {
        $data = $this->fetchApi('/api/v1/about-settings', $request->query());
        return view('about', ['about' => $data]);
    }

    public function contact(HttpRequest $request)
    {
        return view('contact');
    }

    public function blogList(HttpRequest $request)
    {
        $query = array_filter([
            'page' => $request->query('page', 1),
            'limit' => 20,
        ]);
        $data = $this->fetchApi('/api/v1/blogs', $query);
        return view('blog', ['blogsData' => $data]);
    }

    public function blogArticle(HttpRequest $request, string $slug)
    {
        $data = $this->fetchApi('/api/v1/blogs/' . $slug, []);
        return view('blog-post', ['blogData' => $data, 'slug' => $slug]);
    }

    public function projects(HttpRequest $request)
    {
        return view('projects');
    }

    public function compare(HttpRequest $request)
    {
        return view('compare');
    }

    public function tracking(HttpRequest $request, string $trackingId)
    {
        return view('tracking', ['trackingId' => $trackingId]);
    }

    public function wishlist(HttpRequest $request)
    {
        return view('wishlist');
    }

    public function bulkInquiry(HttpRequest $request)
    {
        return view('bulk-inquiry');
    }

    public function kathmandu(HttpRequest $request)
    {
        return view('kathmandu');
    }

    public function category(HttpRequest $request, string $slug)
    {
        $query = array_filter(['category' => $slug, 'limit' => 20, 'page' => $request->query('page', 1)]);
        $data = $this->fetchApi('/api/v1/products', $query);
        return view('category', ['productsData' => $data, 'categorySlug' => $slug]);
    }

    public function quoteStatus(HttpRequest $request)
    {
        return view('quote-status');
    }

    public function brand(HttpRequest $request, string $slug)
    {
        $query = array_filter(['search' => $slug, 'limit' => 20, 'page' => $request->query('page', 1)]);
        $data = $this->fetchApi('/api/v1/products', $query);
        return view('brand', ['productsData' => $data, 'brandSlug' => $slug]);
    }

    public function hospitalType(HttpRequest $request)
    {
        $slug = $request->path();
        $search = str_replace(['hospital-equipment-nepal', 'clinic-equipment-nepal', 'diagnostic-center-equipment-nepal'], ['hospital equipment', 'clinic equipment', 'diagnostic center equipment'], $slug);
        $query = array_filter(['search' => $search, 'limit' => 20, 'page' => $request->query('page', 1)]);
        $data = $this->fetchApi('/api/v1/products', $query);
        return view('hospital-type', ['productsData' => $data, 'pageSlug' => $slug, 'label' => ucwords(str_replace(['-', '/'], ' ', $slug))]);
    }

    public function privacyPolicy(HttpRequest $request)
    {
        $data = $this->fetchApi('/api/v1/legal', []);
        return view('privacy-policy', ['legal' => $data]);
    }

    public function termsConditions(HttpRequest $request)
    {
        $data = $this->fetchApi('/api/v1/legal', []);
        return view('terms-conditions', ['legal' => $data]);
    }

    public function products(HttpRequest $request)
    {
        $query = array_filter([
            'search' => $request->query('search'),
            'category' => $request->query('category'),
            'sort' => $request->query('sort'),
            'page' => $request->query('page', 1),
            'limit' => 20,
        ]);
        $data = $this->fetchApi('/api/v1/products', $query);
        return view('products', ['productsData' => $data, 'query' => $query]);
    }

    public function product(HttpRequest $request, string $idOrSlug)
    {
        $data = $this->fetchApi('/api/v1/products/' . urlencode($idOrSlug), []);
        return view('product-detail', ['productData' => $data]);
    }

    public function adminLogin(HttpRequest $request)
    {
        return view('admin.login');
    }

    public function adminDashboard(HttpRequest $request)
    {
        return view('admin.dashboard');
    }

    public function notFound(HttpRequest $request)
    {
        return response()->view('not-found', [], 404);
    }
}
