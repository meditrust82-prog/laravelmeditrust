<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $req)
    {
        $placement = $req->query('placement');
        $now = now();

        $query = Banner::query()
            ->where('active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });

        if ($placement) {
            $query->where('placement', $placement);
        }

        return response()->json(['banners' => $query->orderByDesc('priority')->latest()->get()]);
    }

    public function click($id)
    {
        Banner::whereKey($id)->increment('clicks');
        return response()->json(['ok' => true]);
    }

    public function adminAll()
    {
        return response()->json(['banners' => Banner::orderByDesc('priority')->latest()->get()]);
    }

    public function store(Request $req, CloudinaryService $cloud)
    {
        $data = $req->validate([
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:240'],
            'linkUrl' => ['nullable', 'string', 'max:500'],
            'linkLabel' => ['nullable', 'string', 'max:60'],
            'bgColor' => ['nullable', 'string', 'max:20'],
            'textColor' => ['nullable', 'string', 'max:20'],
            'placement' => ['required', 'string'],
            'priority' => ['nullable', 'integer'],
            'active' => ['nullable', 'boolean'],
            'startsAt' => ['nullable', 'date'],
            'endsAt' => ['nullable', 'date'],
        ]);

        $imageUrl = null;
        if ($req->hasFile('image')) {
            $uploaded = $cloud->uploadFile($req->file('image'), ['folder' => 'meditrust/banners']);
            $imageUrl = $uploaded['secure_url'] ?? $uploaded['url'] ?? null;
        } elseif ($req->filled('image')) {
            $imageUrl = $req->input('image');
        }

        $banner = Banner::create([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'image_url' => $imageUrl,
            'link_url' => $data['linkUrl'] ?? null,
            'link_label' => $data['linkLabel'] ?? 'Learn More',
            'bg_color' => $data['bgColor'] ?? '#005EEA',
            'text_color' => $data['textColor'] ?? '#ffffff',
            'placement' => $data['placement'],
            'priority' => $data['priority'] ?? 0,
            'active' => $data['active'] ?? true,
            'starts_at' => $data['startsAt'] ?? null,
            'ends_at' => $data['endsAt'] ?? null,
        ]);

        return response()->json(['banner' => $banner], 201);
    }

    public function update(Request $req, $id, CloudinaryService $cloud)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], 404);
        }

        $data = $req->validate([
            'title' => ['sometimes', 'string', 'max:120'],
            'subtitle' => ['sometimes', 'nullable', 'string', 'max:240'],
            'linkUrl' => ['sometimes', 'nullable', 'string', 'max:500'],
            'linkLabel' => ['sometimes', 'nullable', 'string', 'max:60'],
            'bgColor' => ['sometimes', 'nullable', 'string', 'max:20'],
            'textColor' => ['sometimes', 'nullable', 'string', 'max:20'],
            'placement' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'integer'],
            'active' => ['sometimes', 'boolean'],
            'startsAt' => ['sometimes', 'nullable', 'date'],
            'endsAt' => ['sometimes', 'nullable', 'date'],
        ]);

        if ($req->hasFile('image')) {
            $uploaded = $cloud->uploadFile($req->file('image'), ['folder' => 'meditrust/banners']);
            $data['image_url'] = $uploaded['secure_url'] ?? $uploaded['url'] ?? null;
        } elseif ($req->filled('image')) {
            $data['image_url'] = $req->input('image');
        }

        $banner->fill([
            'title' => $data['title'] ?? $banner->title,
            'subtitle' => $data['subtitle'] ?? $banner->subtitle,
            'link_url' => $data['linkUrl'] ?? $banner->link_url,
            'link_label' => $data['linkLabel'] ?? $banner->link_label,
            'bg_color' => $data['bgColor'] ?? $banner->bg_color,
            'text_color' => $data['textColor'] ?? $banner->text_color,
            'placement' => $data['placement'] ?? $banner->placement,
            'priority' => $data['priority'] ?? $banner->priority,
            'active' => array_key_exists('active', $data) ? (bool) $data['active'] : $banner->active,
            'starts_at' => $data['startsAt'] ?? $banner->starts_at,
            'ends_at' => $data['endsAt'] ?? $banner->ends_at,
        ]);
        if (isset($data['image_url'])) {
            $banner->image_url = $data['image_url'];
        }
        $banner->save();

        return response()->json(['banner' => $banner]);
    }

    public function destroy($id)
    {
        Banner::whereKey($id)->delete();
        return response()->json(['ok' => true]);
    }
}
