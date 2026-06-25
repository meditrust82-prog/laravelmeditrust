<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $req)
    {
        $limit = min(max((int) $req->input('limit', 20), 1), 100);
        $page = max((int) $req->input('page', 1), 1);
        $query = Blog::query()->where('published', true)->latest();
        if ($req->filled('category')) {
            $query->where('category', $req->query('category'));
        }
        $total = $query->count();
        $blogs = $query->skip(($page - 1) * $limit)->take($limit)->get()->map(function ($blog) {
            $data = $blog->toArray();
            unset($data['content']);
            return $data;
        })->values();
        return response()->json(['blogs' => $blogs, 'total' => $total]);
    }

    public function all()
    {
        $blogs = Blog::query()->latest()->get();
        return response()->json(['blogs' => $blogs, 'total' => $blogs->count()]);
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->where('published', true)->first();
        if (!$blog) {
            return response()->json(['error' => 'Post not found'], 404);
        }
        return response()->json($blog);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'title' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'string'],
            'author' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'tags' => ['nullable'],
            'published' => ['nullable', 'boolean'],
            'metaTitle' => ['nullable', 'string'],
            'metaDesc' => ['nullable', 'string'],
        ]);

        $blog = Blog::create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'image' => $data['image'] ?? null,
            'author' => $data['author'] ?? 'Meditrust Nepal',
            'category' => $data['category'] ?? null,
            'tags' => is_array($data['tags'] ?? null) ? $data['tags'] : array_values(array_filter(array_map('trim', explode(',', (string) ($data['tags'] ?? ''))))),
            'published' => $data['published'] ?? true,
            'meta_title' => $data['metaTitle'] ?? null,
            'meta_desc' => $data['metaDesc'] ?? null,
        ]);

        return response()->json($blog, 201);
    }

    public function update(Request $req, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['error' => 'Post not found'], 404);
        }
        $data = $req->validate([
            'title' => ['sometimes', 'string'],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'content' => ['sometimes', 'string'],
            'image' => ['sometimes', 'nullable', 'string'],
            'author' => ['sometimes', 'nullable', 'string'],
            'category' => ['sometimes', 'nullable', 'string'],
            'tags' => ['sometimes', 'nullable'],
            'published' => ['sometimes', 'boolean'],
            'metaTitle' => ['sometimes', 'nullable', 'string'],
            'metaDesc' => ['sometimes', 'nullable', 'string'],
        ]);

        if (isset($data['title'])) {
            $blog->title = $data['title'];
            $blog->slug = Str::slug($data['title']);
        }
        if (array_key_exists('excerpt', $data)) $blog->excerpt = $data['excerpt'];
        if (array_key_exists('content', $data)) $blog->content = $data['content'];
        if (array_key_exists('image', $data)) $blog->image = $data['image'];
        if (array_key_exists('author', $data)) $blog->author = $data['author'];
        if (array_key_exists('category', $data)) $blog->category = $data['category'];
        if (array_key_exists('tags', $data)) {
            $blog->tags = is_array($data['tags']) ? $data['tags'] : array_values(array_filter(array_map('trim', explode(',', (string) $data['tags']))));
        }
        if (array_key_exists('published', $data)) $blog->published = (bool) $data['published'];
        if (array_key_exists('metaTitle', $data)) $blog->meta_title = $data['metaTitle'];
        if (array_key_exists('metaDesc', $data)) $blog->meta_desc = $data['metaDesc'];
        $blog->save();

        return response()->json($blog);
    }

    public function destroy($id)
    {
        Blog::whereKey($id)->delete();
        return response()->json(['ok' => true]);
    }
}
