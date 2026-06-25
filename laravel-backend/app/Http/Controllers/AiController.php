<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class AiController extends Controller
{
    protected function groqCall(array $messages, int $maxTokens = 800, float $temperature = 0.5): ?array
    {
        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) {
            return null;
        }

        $response = Http::withToken($apiKey)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ]);

        if (!$response->successful()) {
            return ['error' => $response->json('error.message') ?: 'Groq error'];
        }

        return $response->json();
    }

    protected function productCatalog(): array
    {
        return Product::query()->limit(100)->get()->map(function ($p) {
            return [
                'name' => $p->name,
                'slug' => $p->slug,
                'category' => $p->category,
                'price' => (float) $p->price,
                'description' => $p->description,
                'brand' => $p->brand,
                'badges' => $p->badges ?? [],
                'stock' => $p->stock,
                'quantity' => $p->stock,
                'images' => $p->images,
            ];
        })->all();
    }

    public function chat(Request $req)
    {
        $data = $req->validate([
            'messages' => ['required', 'array', 'min:1'],
            'temperature' => ['nullable', 'numeric'],
            'max_tokens' => ['nullable', 'integer'],
        ]);
        $result = $this->groqCall($data['messages'], (int) ($data['max_tokens'] ?? 800), (float) ($data['temperature'] ?? 0.6));
        if ($result === null) {
            return response()->json(['error' => 'AI service not configured'], 503);
        }
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 500);
        }
        return response()->json($result);
    }

    public function recommend(Request $req)
    {
        $data = $req->validate([
            'productSlug' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'userContext' => ['nullable', 'string'],
        ]);

        $candidates = Product::query();
        if (!empty($data['category'])) {
            $candidates->whereRaw('LOWER(category) = ?', [strtolower($data['category'])]);
        }
        if (!empty($data['productSlug'])) {
            $candidates->where('slug', '!=', $data['productSlug']);
        }
        $items = $candidates->limit(30)->get();
        $recommendations = $items->take(3)->values()->map(function ($p, $idx) {
            return [
                'name' => $p->name,
                'slug' => $p->slug,
                'category' => $p->category,
                'price' => (float) $p->price,
                'brand' => $p->brand,
                'image' => $p->image,
                'reason' => 'Relevant product suggestion based on category and catalog similarity.',
                'match' => 100 - ($idx * 10),
            ];
        })->all();

        if (!env('GROQ_API_KEY')) {
            return response()->json(['error' => 'AI service not configured'], 503);
        }

        $catalog = $items->map(function ($p, $i) {
            return '[' . $i . '] ' . $p->name . ' | Category: ' . ($p->category ?? 'N/A') . ' | Price: ' . ($p->price ? 'NPR ' . $p->price : 'POA');
        })->implode("\n");
        $raw = $this->groqCall([
            ['role' => 'system', 'content' => 'Return exactly 3 recommendations as JSON array with index, reason, match.'],
            ['role' => 'user', 'content' => 'Catalog: ' . $catalog],
        ], 400, 0.3);

        if (isset($raw['error']) || empty($raw['choices'][0]['message']['content'])) {
            return response()->json(['recommendations' => $recommendations]);
        }

        $content = $raw['choices'][0]['message']['content'];
        preg_match('/\[[\s\S]*\]/', $content, $m);
        $parsed = json_decode($m[0] ?? '[]', true) ?: [];
        $out = [];
        foreach ($parsed as $row) {
            if (!isset($items[$row['index']])) {
                continue;
            }
            $p = $items[$row['index']];
            $out[] = [
                'name' => $p->name,
                'slug' => $p->slug,
                'category' => $p->category,
                'price' => (float) $p->price,
                'brand' => $p->brand,
                'image' => $p->image,
                'reason' => $row['reason'] ?? '',
                'match' => $row['match'] ?? 0,
            ];
        }

        return response()->json(['recommendations' => $out ?: $recommendations]);
    }

    public function finder(Request $req)
    {
        $data = $req->validate(['query' => ['required', 'string']]);

        $items = Product::query()->limit(100)->get();
        $results = $items->take(4)->values()->map(function ($p, $idx) {
            return [
                'name' => $p->name,
                'slug' => $p->slug,
                'category' => $p->category,
                'price' => (float) $p->price,
                'brand' => $p->brand,
                'image' => $p->image,
                'reason' => 'Suggested by catalog relevance.',
                'match' => 100 - ($idx * 10),
            ];
        })->all();

        if (!env('GROQ_API_KEY')) {
            return response()->json(['error' => 'AI service not configured'], 503);
        }

        $catalog = $items->map(function ($p, $i) {
            return '[' . $i . '] ' . $p->name . ' | ' . ($p->category ?? 'N/A') . ' | ' . ($p->price ? 'NPR ' . $p->price : 'POA');
        })->implode("\n");
        $raw = $this->groqCall([
            ['role' => 'system', 'content' => 'Return best matching products as JSON array.'],
            ['role' => 'user', 'content' => 'Need: ' . $data['query'] . "\nCatalog:\n" . $catalog],
        ], 500, 0.3);

        if (isset($raw['error']) || empty($raw['choices'][0]['message']['content'])) {
            return response()->json(['results' => $results]);
        }

        preg_match('/\[[\s\S]*\]/', $raw['choices'][0]['message']['content'], $m);
        $parsed = json_decode($m[0] ?? '[]', true) ?: [];
        $out = [];
        foreach ($parsed as $row) {
            if (!isset($items[$row['index']])) {
                continue;
            }
            $p = $items[$row['index']];
            $out[] = [
                'name' => $p->name,
                'slug' => $p->slug,
                'category' => $p->category,
                'price' => (float) $p->price,
                'brand' => $p->brand,
                'image' => $p->image,
                'reason' => $row['reason'] ?? '',
                'match' => $row['match'] ?? 0,
            ];
        }

        return response()->json(['results' => $out ?: $results]);
    }

    public function handle(Request $req, $any)
    {
        return response()->json(['error' => 'Unsupported route'], 404);
    }
}
