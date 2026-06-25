<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $req)
    {
        $all = $req->query('all') === '1' && (($req->user()->role ?? null) === 'admin' || !empty($req->user()->is_admin));
        $query = Testimonial::query()->orderBy('order')->latest();
        if (!$all) {
            $query->where('visible', true);
        }
        return response()->json(['testimonials' => $query->get()]);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name' => ['required', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'organization' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'max:1000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'photoUrl' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'visible' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer'],
        ]);

        $testimonial = Testimonial::create([
            'name' => $data['name'],
            'position' => $data['position'] ?? null,
            'organization' => $data['organization'] ?? null,
            'content' => $data['content'],
            'rating' => $data['rating'] ?? 5,
            'photo_url' => $data['photoUrl'] ?? null,
            'source' => $data['source'] ?? 'direct',
            'visible' => $data['visible'] ?? true,
            'order' => $data['order'] ?? 0,
        ]);

        return response()->json(['testimonial' => $testimonial], 201);
    }

    public function update(Request $req, $id)
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return response()->json(['error' => 'Testimonial not found'], 404);
        }
        $data = $req->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'position' => ['sometimes', 'nullable', 'string', 'max:100'],
            'organization' => ['sometimes', 'nullable', 'string', 'max:100'],
            'content' => ['sometimes', 'string', 'max:1000'],
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'photoUrl' => ['sometimes', 'nullable', 'string'],
            'source' => ['sometimes', 'nullable', 'string'],
            'visible' => ['sometimes', 'boolean'],
            'order' => ['sometimes', 'integer'],
        ]);

        $testimonial->fill([
            'name' => $data['name'] ?? $testimonial->name,
            'position' => $data['position'] ?? $testimonial->position,
            'organization' => $data['organization'] ?? $testimonial->organization,
            'content' => $data['content'] ?? $testimonial->content,
            'rating' => $data['rating'] ?? $testimonial->rating,
            'photo_url' => $data['photoUrl'] ?? $testimonial->photo_url,
            'source' => $data['source'] ?? $testimonial->source,
            'visible' => array_key_exists('visible', $data) ? (bool) $data['visible'] : $testimonial->visible,
            'order' => $data['order'] ?? $testimonial->order,
        ]);
        $testimonial->save();
        return response()->json(['testimonial' => $testimonial]);
    }

    public function destroy($id)
    {
        Testimonial::whereKey($id)->delete();
        return response()->json(['ok' => true]);
    }
}
