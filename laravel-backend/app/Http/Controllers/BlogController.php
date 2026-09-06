<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Product;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Map frontend camelCase keys → snake_case DB columns.
     * Anything not listed here is already 1:1 (title, slug, content, category, etc.).
     */
    protected const FIELD_MAP = [
        'metaTitle' => 'meta_title',
        'metaDesc' => 'meta_desc',
        'altText' => 'alt_text',
        'caption' => 'caption',
        'socialImage' => 'social_image',
        'focusKeyword' => 'focus_keyword',
        'secondaryKeywords' => 'secondary_keywords',
        'searchIntent' => 'search_intent',
        'canonical' => 'canonical',
        'robots' => 'robots',
        'ogTitle' => 'og_title',
        'ogDesc' => 'og_desc',
        'primaryQuestion' => 'primary_question',
        'directAnswer' => 'direct_answer',
        'keyTakeaways' => 'key_takeaways',
        'faqs' => 'faqs',
        'country' => 'country',
        'locations' => 'locations',
        'entities' => 'entities',
        'targetAudience' => 'target_audience',
        'authorBio' => 'author_bio',
        'authorPhoto' => 'author_photo',
        'authorCredentials' => 'author_credentials',
        'authorUrl' => 'author_url',
        'reviewerName' => 'reviewer_name',
        'reviewerDesignation' => 'reviewer_designation',
        'reviewerCredentials' => 'reviewer_credentials',
        'reviewerUrl' => 'reviewer_url',
        'reviewedAt' => 'reviewed_at',
        'sources' => 'sources',
        'relatedBlogs' => 'related_blogs',
        'relatedProducts' => 'related_products',
        'scheduledAt' => 'scheduled_at',
        'publishedAt' => 'published_at',
    ];

    /** String-list columns stored as JSON arrays. */
    protected const ARRAY_FIELDS = [
        'tags', 'secondary_keywords', 'key_takeaways', 'locations', 'entities', 'target_audience',
    ];

    /** Object-list columns stored as JSON arrays of assoc arrays. */
    protected const OBJECT_FIELDS = ['faqs', 'sources', 'related_blogs', 'related_products'];

    public function index(Request $req)
    {
        $limit = min(max((int) $req->input('limit', 20), 1), 100);
        $page = max((int) $req->input('page', 1), 1);
        $query = Blog::query()->where('published', true)
            ->where(function ($q) {
                $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            })
            ->latest();

        if ($req->filled('category')) {
            $query->where('category', $req->query('category'));
        }
        if ($req->filled('search')) {
            $term = '%' . $req->string('search')->trim()->toString() . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('excerpt', 'like', $term)
                  ->orWhere('category', 'like', $term);
            });
        }

        $total = $query->count();
        $blogs = $query->skip(($page - 1) * $limit)->take($limit)->get()->map(function ($blog) {
            $data = $blog->toArray();
            unset($data['content']);
            return $data;
        })->values();

        return response()->json(['blogs' => $blogs, 'total' => $total]);
    }

    public function all(Request $req)
    {
        $query = Blog::query()->latest();

        if ($req->filled('search')) {
            $term = '%' . $req->string('search')->trim()->toString() . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('excerpt', 'like', $term)
                  ->orWhere('category', 'like', $term)
                  ->orWhere('author', 'like', $term);
            });
        }
        if ($req->filled('category')) {
            $query->where('category', $req->query('category'));
        }
        if ($req->filled('status')) {
            $status = $req->query('status');
            if ($status === 'published') {
                $query->where('published', true);
            } elseif ($status === 'draft') {
                $query->where('published', false)->whereNull('scheduled_at');
            } elseif ($status === 'scheduled') {
                $query->whereNotNull('scheduled_at')->where('scheduled_at', '>', now());
            }
        }

        $blogs = $query->get();
        return response()->json(['blogs' => $blogs, 'total' => $blogs->count()]);
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->where('published', true)
            ->where(function ($q) {
                $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            })
            ->first();
        if (!$blog) {
            return response()->json(['error' => 'Post not found'], 404);
        }
        return response()->json($blog);
    }

    /** Resolve slugs for related blogs / products to {slug,title} objects. */
    protected function resolveRelated(array $value, string $type): array
    {
        $out = [];
        foreach ($value as $item) {
            if (is_string($item)) {
                $out[] = ['slug' => $item, 'title' => null];
                continue;
            }
            if (is_array($item) && !empty($item['slug'])) {
                $out[] = [
                    'slug' => $item['slug'],
                    'title' => $item['title'] ?? $item['name'] ?? null,
                ];
            }
        }
        return $out;
    }

    protected function normalizeArray($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(fn ($v) => trim((string) $v), $value)));
        }
        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }
        return [];
    }

    protected function normalizeObjectArray($value): array
    {
        if (!is_array($value)) {
            return [];
        }
        return array_values(array_filter($value, fn ($v) => is_array($v)));
    }

    protected function wordCount(string $html): int
    {
        $text = html_entity_decode(strip_tags($html));
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);
        if ($text === '') {
            return 0;
        }
        return count(preg_split('/\s+/u', $text));
    }

    protected function readingTime(int $words): int
    {
        return max(1, (int) ceil($words / 200));
    }

    protected function parsePayload(Request $req): array
    {
        $data = [];
        $all = $req->all();

        // 1:1 fields
        $direct = ['title', 'slug', 'excerpt', 'content', 'image', 'author', 'category', 'canonical', 'robots'];
        foreach ($direct as $key) {
            if ($req->has($key)) {
                $data[$key] = $req->input($key);
            }
        }

        // camelCase → snake_case mapped fields
        foreach (self::FIELD_MAP as $camel => $snake) {
            if ($req->has($camel)) {
                $data[$snake] = $req->input($camel);
            }
        }

        // Arrays (string-list)
        foreach (self::ARRAY_FIELDS as $field) {
            $camel = Str::camel($field);
            if ($req->has($camel) || $req->has($field)) {
                $data[$field] = $this->normalizeArray($req->input($camel, $req->input($field)));
            }
        }

        // Object arrays
        foreach (self::OBJECT_FIELDS as $field) {
            $camel = Str::camel($field);
            if ($req->has($camel) || $req->has($field)) {
                $data[$field] = $this->normalizeObjectArray($req->input($camel, $req->input($field)));
            }
        }

        // related blogs / products: store {slug,title} so frontend can link without extra lookups
        if (isset($data['related_blogs'])) {
            $data['related_blogs'] = $this->resolveRelated($data['related_blogs'], 'blog');
        }
        if (isset($data['related_products'])) {
            $data['related_products'] = $this->resolveRelated($data['related_products'], 'product');
        }

        // Publishing / status
        if ($req->has('published')) {
            $data['published'] = (bool) $req->input('published');
        }
        if (array_key_exists('scheduled_at', $data) && $data['scheduled_at']) {
            $data['scheduled_at'] = \Illuminate\Support\Carbon::parse($data['scheduled_at']);
        }
        if (array_key_exists('scheduled_at', $data) && empty($data['scheduled_at'])) {
            $data['scheduled_at'] = null;
        }
        if (!empty($data['published']) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        if (isset($data['reviewed_at']) && $data['reviewed_at']) {
            $data['reviewed_at'] = \Illuminate\Support\Carbon::parse($data['reviewed_at'])->toDateString();
        }

        // Derived metrics
        if (isset($data['content'])) {
            $words = $this->wordCount((string) $data['content']);
            $data['word_count'] = $words;
            $data['reading_time'] = $this->readingTime($words);
        }

        return $data;
    }

    public function store(Request $req)
    {
        $req->validate([
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
        ]);

        $data = $this->parsePayload($req);

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['title']);

        $blog = Blog::create(array_merge($data, [
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'author' => $data['author'] ?? 'Meditrust Nepal',
            'published' => $data['published'] ?? true,
            'word_count' => $data['word_count'] ?? $this->wordCount($data['content']),
            'reading_time' => $data['reading_time'] ?? $this->readingTime($data['word_count'] ?? 0),
        ]));

        return response()->json($blog->fresh(), 201);
    }

    public function update(Request $req, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $data = $this->parsePayload($req);

        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        if (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
            if ($data['slug'] === '') {
                $data['slug'] = Str::slug($data['title'] ?? $blog->title);
            }
        }

        // Ensure slug stays unique (skip self)
        if (!empty($data['slug'])) {
            $exists = Blog::where('slug', $data['slug'])->where('id', '!=', $blog->id)->exists();
            if ($exists) {
                $data['slug'] = $data['slug'] . '-' . $blog->id;
            }
        }

        $blog->fill($data)->save();

        return response()->json($blog->fresh());
    }

    public function destroy($id)
    {
        Blog::whereKey($id)->delete();
        return response()->json(['ok' => true]);
    }

    public function duplicate($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $copy = $blog->replicate();
        $copy->title = $blog->title . ' (Copy)';
        $copy->slug = $blog->slug . '-copy-' . now()->timestamp;
        $copy->published = false;
        $copy->published_at = null;
        $copy->scheduled_at = null;
        $copy->created_at = now();
        $copy->updated_at = now();
        $copy->save();

        return response()->json($copy->fresh(), 201);
    }

    /** Upload a featured/social image directly to Cloudinary. */
    public function upload(Request $req, CloudinaryService $cloud)
    {
        $req->validate(['image' => ['required', 'image', 'max:8192']]);
        $uploaded = $cloud->uploadFile($req->file('image'), ['folder' => 'meditrust/blog']);
        $url = $uploaded['secure_url'] ?? $uploaded['url'] ?? null;
        if (!$url) {
            return response()->json(['error' => 'Upload failed'], 500);
        }
        return response()->json(['url' => $url, 'public_id' => $uploaded['public_id'] ?? null]);
    }
}
