<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'productName' => ['required', 'string'],
            'productSlug' => ['nullable', 'string'],
            'name' => ['required', 'string'],
            'hospitalName' => ['nullable', 'string'],
            'phone' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'message' => ['nullable', 'string'],
            'qty' => ['nullable', 'integer'],
            'source' => ['nullable', 'string'],
        ]);

        $quote = Quote::create([
            'product_name' => $data['productName'],
            'product_slug' => $data['productSlug'] ?? null,
            'name' => $data['name'],
            'hospital_name' => $data['hospitalName'] ?? null,
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'message' => $data['message'] ?? null,
            'qty' => $data['qty'] ?? 1,
            'source' => $data['source'] ?? 'product_detail',
            'status' => 'new',
        ]);

        return response()->json(['ok' => true, 'quoteId' => $quote->id], 201);
    }

    public function my(Request $req)
    {
        $phone = $req->query('phone');
        if (!$phone) {
            return response()->json(['error' => 'phone is required'], 400);
        }
        $digits = preg_replace('/[^0-9]/', '', $phone);
        $quotes = Quote::query()
            ->where('phone', 'like', '%' . $digits . '%')
            ->latest()
            ->get()
            ->map(function ($quote) {
                return [
                    'productName'  => $quote->productName,
                    'hospitalName' => $quote->hospitalName,
                    'status'       => $quote->status,
                    'createdAt'    => $quote->createdAt,
                    'message'      => $quote->message,
                ];
            });

        return response()->json(['quotes' => $quotes]);
    }

    public function index(Request $req)
    {
        $limit = min(max((int) $req->input('limit', 50), 1), 100);
        $page = max((int) $req->input('page', 1), 1);
        $query = Quote::query()->latest();
        if ($req->filled('status')) {
            $query->where('status', $req->query('status'));
        }
        $total = $query->count();
        $quotes = $query->skip(($page - 1) * $limit)->take($limit)->get();
        return response()->json(['quotes' => $quotes, 'total' => $total]);
    }

    public function stats()
    {
        return response()->json([
            'total' => Quote::count(),
            'new' => Quote::where('status', 'new')->count(),
            'contacted' => Quote::where('status', 'contacted')->count(),
            'converted' => Quote::where('status', 'converted')->count(),
            'lost' => Quote::where('status', 'lost')->count(),
        ]);
    }

    public function update(Request $req, $id)
    {
        $quote = Quote::find($id);
        if (!$quote) {
            return response()->json(['error' => 'Quote not found'], 404);
        }
        $data = $req->validate([
            'status' => ['sometimes', 'string'],
            'adminNotes' => ['sometimes', 'nullable', 'string'],
        ]);
        if (array_key_exists('status', $data)) $quote->status = $data['status'];
        if (array_key_exists('adminNotes', $data)) $quote->admin_notes = $data['adminNotes'];
        $quote->save();
        return response()->json(['quote' => $quote]);
    }

    public function destroy($id)
    {
        Quote::whereKey($id)->delete();
        return response()->json(['ok' => true]);
    }
}
