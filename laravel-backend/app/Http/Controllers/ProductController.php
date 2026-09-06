<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Quote;
use App\Services\CloudinaryService;

class ProductController extends Controller
{
    protected function isAdmin(Request $req): bool
    {
        $user = $req->user() ?: auth()->user();
        return $user && (($user->role ?? null) === 'admin' || !empty($user->is_admin));
    }

    protected function normalize(array $product): array
    {
        $images = $product['images'] ?? [];
        $flatImages = array_values(array_filter(array_map(function ($img) {
            if (is_array($img)) {
                return $img['url'] ?? $img['path'] ?? null;
            }
            return $img;
        }, $images)));

        $product['id'] = $product['id'] ?? ($product['_id'] ?? null);
        $product['image'] = $product['image'] ?? ($flatImages[0] ?? null);
        $product['allImages'] = $product['allImages'] ?? $flatImages;
        $product['categorySlug'] = $product['categorySlug'] ?? Str::of((string) ($product['category'] ?? ''))->slug()->toString();
        $product['quantity'] = $product['quantity'] ?? ($product['stock'] ?? null);
        return $product;
    }

    public function index(Request $req)
    {
        $query = Product::query();
        $admin = $this->isAdmin($req);

        if ($req->filled('category')) {
            $category = $req->string('category')->toString();
            $query->whereRaw('LOWER(category) = ?', [strtolower($category)]);
        }
        if ($req->filled('featured') && $req->string('featured')->toString() === 'true') {
            $query->where('featured', true);
        }
        if ($req->filled('search')) {
            $term = '%' . $req->string('search')->trim()->toString() . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhere('category', 'like', $term)
                  ->orWhere('brand', 'like', $term);
            });
        }

        $sort = $req->string('sort')->toString() ?: '-created_at';
        if ($sort === '-createdAt') {
            $sort = '-created_at';
        }
        $allowed = ['-created_at', 'created_at', '-price', 'price', '-name', 'name', '-updated_at'];
        if (!in_array($sort, $allowed, true)) {
            $sort = '-created_at';
        }

        $limit = max(1, min(100, (int) $req->input('limit', 12)));
        $page = max(1, (int) $req->input('page', 1));
        $offset = ($page - 1) * $limit;

        $total = $query->count();
        $products = $query->orderBy(ltrim($sort, '-'), str_starts_with($sort, '-') ? 'desc' : 'asc')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(fn ($product) => $this->normalize($product->toArray()))
            ->values()
            ->all();

        if (!$admin) {
            $products = array_map(function ($product) {
                unset($product['cost']);
                return $product;
            }, $products);
        }

        $categories = Product::whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->distinct()
            ->pluck('category')
            ->values()
            ->all();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'total' => $total,
            'page' => $page,
            'pages' => (int) ceil($total / $limit),
        ]);
    }

    public function show(Request $req, $idOrSlug)
    {
        $admin = $this->isAdmin($req);
        $product = Product::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $data = $this->normalize($product->toArray());
        if (!$admin) {
            unset($data['cost']);
        }

        return response()->json($data);
    }

    protected function decodeJsonArray($value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }
        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values($decoded);
            }
        }
        return [];
    }

    protected function parseImages(Request $req): array
    {
        $images = [];
        foreach ($req->file('images', []) as $file) {
            $uploaded = app(CloudinaryService::class)->uploadFile($file, ['folder' => 'meditrust/products']);
            $images[] = [
                'url' => $uploaded['secure_url'] ?? $uploaded['url'] ?? null,
                'alt' => $req->input('name', ''),
            ];
        }
        return array_values(array_filter($images, fn ($img) => !empty($img['url'])));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'regex:/^[a-z0-9-]+$/'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:255'],
            'originalPrice' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'featured' => ['nullable', 'boolean'],
            'metaTitle' => ['nullable', 'string'],
            'metaDescription' => ['nullable', 'string'],
            'metaKeywords' => ['nullable', 'string'],
            'focusKeyword' => ['nullable', 'string'],
            'canonical' => ['nullable', 'string'],
            'robots' => ['nullable', 'string'],
            'ogTitle' => ['nullable', 'string'],
            'ogDesc' => ['nullable', 'string'],
            'ogImage' => ['nullable', 'string'],
            'primaryQuestion' => ['nullable', 'string'],
            'directAnswer' => ['nullable', 'string'],
            'keyTakeaways' => ['nullable'],
            'faqs' => ['nullable'],
            'country' => ['nullable', 'string'],
            'locations' => ['nullable'],
            'entities' => ['nullable'],
            'targetAudience' => ['nullable'],
            'searchIntent' => ['nullable', 'string'],
            'badges' => ['nullable'],
        ]);

        $product = Product::create([
            'name' => $data['name'],
            'slug' => strtolower($data['slug']),
            'description' => $data['description'] ?? null,
            'specifications' => $data['specifications'] ?? null,
            'brand' => $data['brand'] ?? null,
            'price' => $data['price'],
            'original_price' => $data['originalPrice'] ?? null,
            'cost' => $data['cost'] ?? 0,
            'category' => $data['category'],
            'images' => $this->parseImages($req),
            'stock' => $data['stock'] ?? 0,
            'featured' => (bool) ($data['featured'] ?? false),
            'meta_title' => $data['metaTitle'] ?? null,
            'meta_description' => $data['metaDescription'] ?? null,
            'meta_keywords' => $data['metaKeywords'] ?? null,
            'focus_keyword' => $data['focusKeyword'] ?? null,
            'canonical' => $data['canonical'] ?? null,
            'robots' => $data['robots'] ?? 'index,follow',
            'og_title' => $data['ogTitle'] ?? null,
            'og_desc' => $data['ogDesc'] ?? null,
            'og_image' => $data['ogImage'] ?? null,
            'primary_question' => $data['primaryQuestion'] ?? null,
            'direct_answer' => $data['directAnswer'] ?? null,
            'key_takeaways' => $this->decodeJsonArray($data['keyTakeaways'] ?? null),
            'faqs' => $this->decodeJsonArray($data['faqs'] ?? null),
            'country' => $data['country'] ?? 'Nepal',
            'locations' => $this->decodeJsonArray($data['locations'] ?? null),
            'entities' => $this->decodeJsonArray($data['entities'] ?? null),
            'target_audience' => $this->decodeJsonArray($data['targetAudience'] ?? null),
            'search_intent' => $data['searchIntent'] ?? 'transactional',
            'badges' => is_array($data['badges'] ?? null) ? $data['badges'] : array_values(array_filter(array_map('trim', explode('|', (string) ($data['badges'] ?? ''))))),
        ]);

        return response()->json($this->normalize($product->fresh()->toArray()), 201);
    }

    public function update(Request $req, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $data = $req->validate([
            'name' => ['sometimes', 'string', 'max:200'],
            'slug' => ['sometimes', 'string', 'regex:/^[a-z0-9-]+$/'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'category' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'specifications' => ['sometimes', 'nullable', 'string'],
            'brand' => ['sometimes', 'nullable', 'string', 'max:255'],
            'originalPrice' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'cost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'featured' => ['sometimes', 'boolean'],
            'metaTitle' => ['sometimes', 'nullable', 'string'],
            'metaDescription' => ['sometimes', 'nullable', 'string'],
            'metaKeywords' => ['sometimes', 'nullable', 'string'],
            'focusKeyword' => ['sometimes', 'nullable', 'string'],
            'canonical' => ['sometimes', 'nullable', 'string'],
            'robots' => ['sometimes', 'nullable', 'string'],
            'ogTitle' => ['sometimes', 'nullable', 'string'],
            'ogDesc' => ['sometimes', 'nullable', 'string'],
            'ogImage' => ['sometimes', 'nullable', 'string'],
            'primaryQuestion' => ['sometimes', 'nullable', 'string'],
            'directAnswer' => ['sometimes', 'nullable', 'string'],
            'keyTakeaways' => ['sometimes', 'nullable'],
            'faqs' => ['sometimes', 'nullable'],
            'country' => ['sometimes', 'nullable', 'string'],
            'locations' => ['sometimes', 'nullable'],
            'entities' => ['sometimes', 'nullable'],
            'targetAudience' => ['sometimes', 'nullable'],
            'searchIntent' => ['sometimes', 'nullable', 'string'],
            'badges' => ['sometimes'],
        ]);

        $mapped = [
            'name' => $data['name'] ?? $product->name,
            'slug' => isset($data['slug']) ? strtolower($data['slug']) : $product->slug,
            'description' => $data['description'] ?? $product->description,
            'specifications' => $data['specifications'] ?? $product->specifications,
            'brand' => $data['brand'] ?? $product->brand,
            'price' => $data['price'] ?? $product->price,
            'original_price' => $data['originalPrice'] ?? $product->original_price,
            'cost' => $data['cost'] ?? $product->cost,
            'category' => $data['category'] ?? $product->category,
            'stock' => $data['stock'] ?? $product->stock,
            'featured' => array_key_exists('featured', $data) ? (bool) $data['featured'] : $product->featured,
            'meta_title' => $data['metaTitle'] ?? $product->meta_title,
            'meta_description' => $data['metaDescription'] ?? $product->meta_description,
            'meta_keywords' => $data['metaKeywords'] ?? $product->meta_keywords,
            'focus_keyword' => $data['focusKeyword'] ?? $product->focus_keyword,
            'canonical' => $data['canonical'] ?? $product->canonical,
            'robots' => $data['robots'] ?? $product->robots,
            'og_title' => $data['ogTitle'] ?? $product->og_title,
            'og_desc' => $data['ogDesc'] ?? $product->og_desc,
            'og_image' => $data['ogImage'] ?? $product->og_image,
            'primary_question' => $data['primaryQuestion'] ?? $product->primary_question,
            'direct_answer' => $data['directAnswer'] ?? $product->direct_answer,
            'key_takeaways' => array_key_exists('keyTakeaways', $data) ? $this->decodeJsonArray($data['keyTakeaways']) : $product->key_takeaways,
            'faqs' => array_key_exists('faqs', $data) ? $this->decodeJsonArray($data['faqs']) : $product->faqs,
            'country' => $data['country'] ?? $product->country,
            'locations' => array_key_exists('locations', $data) ? $this->decodeJsonArray($data['locations']) : $product->locations,
            'entities' => array_key_exists('entities', $data) ? $this->decodeJsonArray($data['entities']) : $product->entities,
            'target_audience' => array_key_exists('targetAudience', $data) ? $this->decodeJsonArray($data['targetAudience']) : $product->target_audience,
            'search_intent' => $data['searchIntent'] ?? $product->search_intent,
            'badges' => array_key_exists('badges', $data)
                ? (is_array($data['badges']) ? $data['badges'] : array_values(array_filter(array_map('trim', explode('|', (string) $data['badges'])))))
                : $product->badges,
        ];

        if ($req->hasFile('images')) {
            $mapped['images'] = $this->parseImages($req);
        }

        $product->fill($mapped)->save();

        return response()->json($this->normalize($product->fresh()->toArray()));
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    protected function csvFriendlyHeaders(): array
    {
        return [
            'name' => 'name',
            'slug' => 'slug',
            'category' => 'category',
            'brand' => 'brand',
            'price' => 'price',
            'originalPrice' => 'originalPrice',
            'stock' => 'stock',
            'description' => 'description',
            'metaTitle' => 'metaTitle',
            'metaDescription' => 'metaDescription',
            'metaKeywords' => 'metaKeywords',
            'featured' => 'featured',
            'badges' => 'badges',
        ];
    }

    protected function normalizeCsvHeader(string $header): string
    {
        $header = trim($header);
        $aliases = [
            'originalprice' => 'original_price',
            'original_price' => 'original_price',
            'metatitle' => 'meta_title',
            'meta_title' => 'meta_title',
            'metadescription' => 'meta_description',
            'meta_description' => 'meta_description',
            'metakeywords' => 'meta_keywords',
            'meta_keywords' => 'meta_keywords',
            'quantity' => 'stock',
            'image' => 'image',
        ];

        $key = strtolower(preg_replace('/[^a-z0-9]+/', '', $header));
        if (isset($aliases[$key])) {
            return $aliases[$key];
        }

        if ($header === '') {
            return $header;
        }

        return (string) Str::snake(preg_replace('/[^A-Za-z0-9]/', '', $header));
    }

    public function exportCsv()
    {
        $products = Product::query()->get();
        $cols = array_keys($this->csvFriendlyHeaders());
        $rows = [implode(',', $cols)];
        foreach ($products as $p) {
            $product = $p->toArray();
            $row = [];
            foreach ($this->csvFriendlyHeaders() as $field => $header) {
                $value = $product[$field] ?? $product[\Illuminate\Support\Str::snake($field)] ?? null;
                if ($field === 'badges') {
                    $value = implode('|', $value ?? []);
                } elseif ($field === 'featured') {
                    $value = $value ? 'true' : 'false';
                }
                $row[] = '"' . str_replace('"', '""', (string) $value) . '"';
            }
            $rows[] = implode(',', $row);
        }

        return response(implode("\r\n", $rows), 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="products-' . now()->timestamp . '.csv"');
    }

    public function exportQuotesCsv()
    {
        $quotes = Quote::query()->get();
        $cols = ['product_name', 'name', 'hospital_name', 'phone', 'email', 'message', 'status', 'source', 'admin_notes', 'created_at'];
        $rows = [implode(',', $cols)];
        foreach ($quotes as $q) {
            $row = [];
            foreach ($cols as $col) {
                $createdAt = $q->getRawOriginal('created_at') ? \Illuminate\Support\Carbon::parse($q->getRawOriginal('created_at')) : null;
                $value = $col === 'created_at' ? optional($createdAt)?->toIso8601String() : $q->{$col};
                $row[] = '"' . str_replace('"', '""', (string) $value) . '"';
            }
            $rows[] = implode(',', $row);
        }
        return response(implode("\r\n", $rows), 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="quotes-' . now()->timestamp . '.csv"');
    }

    public function importCsv(Request $req)
    {
        if (!$req->hasFile('csv')) {
            return response()->json(['error' => 'No CSV file uploaded'], 400);
        }
        $text = file_get_contents($req->file('csv')->getRealPath());
        $lines = array_values(array_filter(preg_split('/\r?\n/', $text)));
        if (count($lines) < 2) {
            return response()->json(['error' => 'CSV must have a header row and at least one data row'], 400);
        }
        $rawHeaders = array_map(fn ($h) => trim(trim($h), '"'), str_getcsv(array_shift($lines)));
        $headers = array_map(fn ($h) => $this->normalizeCsvHeader($h), $rawHeaders);
        $stats = ['created' => 0, 'updated' => 0, 'errors' => []];

        foreach ($lines as $idx => $line) {
            try {
                $values = str_getcsv($line);
                $row = array_combine($headers, array_pad($values, count($headers), null));
                if (empty($row['name'])) {
                    continue;
                }
                $slug = $row['slug'] ?: Str::of($row['name'])->slug()->toString();
                $payload = [
                    'name' => $row['name'],
                    'slug' => $slug,
                    'category' => $row['category'] ?: 'General',
                    'brand' => $row['brand'] ?: null,
                    'price' => (float) ($row['price'] ?? 0),
                    'original_price' => isset($row['original_price']) && $row['original_price'] !== '' ? (float) $row['original_price'] : null,
                    'stock' => isset($row['stock']) && $row['stock'] !== '' ? (int) $row['stock'] : 0,
                    'description' => $row['description'] ?: null,
                    'meta_title' => $row['meta_title'] ?: null,
                    'meta_description' => $row['meta_description'] ?: null,
                    'meta_keywords' => $row['meta_keywords'] ?: null,
                    'featured' => ($row['featured'] ?? 'false') === 'true',
                    'badges' => !empty($row['badges']) ? array_values(array_filter(array_map('trim', explode('|', $row['badges'] ?? '')))) : [],
                ];
                $existing = Product::where('slug', $slug)->first();
                if ($existing) {
                    $existing->fill($payload)->save();
                    $stats['updated']++;
                } else {
                    Product::create($payload);
                    $stats['created']++;
                }
            } catch (\Throwable $e) {
                $stats['errors'][] = 'Row ' . ($idx + 2) . ': ' . $e->getMessage();
            }
        }

        return response()->json(['ok' => true] + $stats);
    }
}
