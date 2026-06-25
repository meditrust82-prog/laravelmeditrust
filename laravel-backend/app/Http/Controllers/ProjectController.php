<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $req)
    {
        $limit = min(max((int) $req->input('limit', 50), 1), 100);
        $page = max((int) $req->input('page', 1), 1);
        $admin = (($req->user()->role ?? null) === 'admin') || !empty($req->user()->is_admin);

        $query = Project::query()
            ->when(!$admin, fn ($q) => $q->where('published', true))
            ->when($req->filled('category'), fn ($q) => $q->where('category', $req->query('category')))
            ->when($req->filled('search'), function ($q) use ($req) {
                $term = '%' . trim((string) $req->query('search')) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('category', 'like', $term)
                        ->orWhere('location', 'like', $term)
                        ->orWhere('client', 'like', $term);
                });
            })
            ->orderBy('sort_order')
            ->latest();

        $total = $query->count();
        $projects = $query->skip(($page - 1) * $limit)->take($limit)->get();

        return response()->json([
            'projects' => $projects,
            'total' => $total,
            'page' => $page,
            'pages' => (int) ceil($total / $limit),
        ]);
    }

    public function store(Request $req)
    {
        $project = Project::create($this->validated($req));
        return response()->json(['project' => $project], 201);
    }

    public function update(Request $req, $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $project->fill($this->validated($req, true))->save();
        return response()->json(['project' => $project->fresh()]);
    }

    public function destroy($id)
    {
        Project::whereKey($id)->delete();
        return response()->json(['ok' => true]);
    }

    protected function validated(Request $req, bool $partial = false): array
    {
        $sometimes = $partial ? 'sometimes' : 'required';
        $data = $req->validate([
            'title' => [$sometimes, 'string', 'max:200'],
            'category' => [$sometimes, 'string', 'max:100'],
            'description' => [$sometimes, 'string'],
            'images' => ['sometimes', 'nullable'],
            'year' => ['sometimes', 'nullable', 'string', 'max:20'],
            'location' => ['sometimes', 'nullable', 'string', 'max:150'],
            'client' => ['sometimes', 'nullable', 'string', 'max:150'],
            'featured' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer'],
            'published' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('images', $data) && !is_array($data['images'])) {
            $data['images'] = array_values(array_filter(array_map('trim', explode(',', (string) $data['images']))));
        }

        if (array_key_exists('sortOrder', $data)) {
            $data['sort_order'] = $data['sortOrder'];
            unset($data['sortOrder']);
        }

        return $data;
    }
}
