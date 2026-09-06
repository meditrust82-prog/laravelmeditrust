<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;
use App\Models\Product;

class AiController extends Controller
{
    protected function groqCall(array $messages, int $maxTokens = 800, float $temperature = 0.5): ?array
    {
        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) {
            return null;
        }

        $models = array_values(array_unique([
            env('GROQ_MODEL', 'qwen/qwen3.6-27b'),
            'openai/gpt-oss-120b',
            'openai/gpt-oss-20b',
        ]));
        $errors = [];

        foreach ($models as $model) {
            try {
                $response = Http::withToken($apiKey)->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                $errors[] = sprintf(
                    '%s (%s): %s',
                    $model,
                    $response->status(),
                    $response->json('error.message') ?: 'Groq request failed'
                );
            } catch (Throwable $exception) {
                $errors[] = sprintf('%s: %s', $model, $exception->getMessage());
            }
        }

        return ['error' => 'All Groq models failed: ' . implode(' | ', $errors)];
    }

    /** xAI Grok (OpenAI-compatible). Prefer XAI_API_KEY, fall back to GROK_API_KEY. */
    protected function xaiCall(array $messages, int $maxTokens = 800, float $temperature = 0.5): ?array
    {
        $apiKey = env('XAI_API_KEY') ?: env('GROK_API_KEY');
        if (!$apiKey) {
            return null;
        }

        $model = env('GROK_MODEL', 'grok-2-latest');
        $response = Http::withToken($apiKey)->post('https://api.x.ai/v1/chat/completions', [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ]);

        if (!$response->successful()) {
            return ['error' => $response->json('error.message') ?: 'Grok error'];
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

    /** Extract a JSON object from an AI response (strips markdown fences, handles prose). */
    protected function extractJson(string $text): ?array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $text, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Generate a full blog post (title, content, SEO, AEO, FAQs, author…) from a
     * topic description, structured to match the Meditrust blog schema.
     */
    public function generateBlog(Request $req)
    {
        $data = $req->validate([
            'prompt' => ['required', 'string', 'min:5'],
            'tone' => ['nullable', 'string', 'max:120'],
            'audience' => ['nullable', 'string', 'max:200'],
        ]);

        if (!env('GROQ_API_KEY') && !env('XAI_API_KEY') && !env('GROK_API_KEY')) {
            return response()->json(['error' => 'AI service not configured (set XAI_API_KEY or GROQ_API_KEY)'], 503);
        }

        $tone = $data['tone'] ?? 'professional and helpful';
        $audience = $data['audience'] ?? 'medical professionals and buyers in Nepal';

        $system = <<<'SYS'
You are an expert medical-equipment content writer for Meditrust Nepal, an eCommerce platform for medical equipment in Nepal.

Given a topic description, write a complete, well-structured blog post. Return ONLY a valid JSON object (no markdown code fences, no commentary before or after) with EXACTLY these keys:

{
  "title": "compelling SEO title, 60 chars max",
  "slug": "lowercase url slug",
  "excerpt": "1-2 sentence summary, 155 chars max",
  "content": "full article HTML using ONLY these tags: h2, h3, p, ul, ol, li, blockquote, strong, em. Do NOT use h1 or markdown. 800-1200 words, 3-6 h2 sections, include at least one h3 subsection under the first h2 and at least one bullet or numbered list.",
  "category": "a short category name",
  "tags": ["4-6 short tags"],
  "metaTitle": "SEO title, 60 chars max",
  "metaDesc": "meta description, 155 chars max",
  "focusKeyword": "primary keyword phrase",
  "secondaryKeywords": ["2-4 related keyword phrases"],
  "searchIntent": "one of: informational, commercial, transactional, navigational",
  "primaryQuestion": "the main question the article answers",
  "directAnswer": "40-80 word direct answer suitable for a featured snippet",
  "keyTakeaways": ["4-5 key takeaways"],
  "faqs": [{"q": "question", "a": "answer"}],
  "author": "Meditrust Nepal",
  "authorCredentials": "short author credential line",
  "authorBio": "1-2 sentence author bio",
  "sources": [{"title": "source title", "url": "https://...", "publisher": "publisher name", "type": "guideline|study|standard"}]
}

Keep the content factual and general. Do not invent specific prices, stock, or unverifiable medical claims. Write in the requested tone for the requested audience.
SYS;

        $user = 'Topic: ' . $data['prompt']
            . "\nTone: " . $tone
            . "\nTarget audience: " . $audience;

        $raw = $this->xaiCall([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ], 4000, 0.7);

        if ($raw === null) {
            $raw = $this->groqCall([
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ], 4000, 0.7);
        }

        if ($raw === null) {
            return response()->json(['error' => 'AI service not configured'], 503);
        }
        if (isset($raw['error'])) {
            return response()->json(['error' => $raw['error']], 500);
        }

        $text = $raw['choices'][0]['message']['content'] ?? '';
        $parsed = $this->extractJson($text);

        if (!$parsed) {
            return response()->json(['error' => 'AI returned an unparseable response', 'raw' => $text], 422);
        }

        return response()->json($parsed);
    }

    public function handle(Request $req, $any)
    {
        return response()->json(['error' => 'Unsupported route'], 404);
    }
}
