<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product' => ['required'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'shippingAddress.name' => ['required', 'string', 'max:100'],
            'shippingAddress.phone' => ['required', 'string', 'max:30'],
            'shippingAddress.addressLine' => ['required', 'string', 'max:300'],
            'shippingAddress.city' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'gateway' => ['nullable', 'in:khalti'],
        ]);

        try {
            return DB::transaction(function () use ($req, $data) {
            $productIds = collect($data['items'])->pluck('product')->all();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
            $resolved = [];
            $total = 0;

            foreach ($data['items'] as $item) {
                $product = $products->get((string) $item['product']) ?? $products->get($item['product']);
                if (!$product) {
                    throw new \RuntimeException('Product ' . $item['product'] . ' not found');
                }
                if ((int) $product->stock < (int) $item['quantity']) {
                    throw new \RuntimeException('Insufficient stock for "' . $product->name . '"');
                }
                $product->decrement('stock', (int) $item['quantity']);
                $total += ((float) $product->price) * (int) $item['quantity'];
                $resolved[] = [
                    'product' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'price' => (float) $product->price,
                    'cost' => (float) $product->cost,
                    'quantity' => (int) $item['quantity'],
                ];
            }

            $status = ($data['gateway'] ?? null) === 'khalti' ? 'awaiting_payment' : 'pending';
            $order = Order::create([
                'user_id' => auth()->id(),
                'items' => $resolved,
                'total_price' => $total,
                'status' => $status,
                'payment_gateway' => $data['gateway'] ?? null,
                'payment_amount' => $status === 'awaiting_payment' ? $total : null,
                'payment_expires_at' => $status === 'awaiting_payment' ? now()->addMinutes(30) : null,
                'shipping_address' => $data['shippingAddress'],
                'notes' => $data['notes'] ?? null,
            ]);

            OrderService::audit('order.created', $order->id, [
                'user' => (string) auth()->id(),
                'totalPrice' => $total,
                'status' => $status,
                'gateway' => $data['gateway'] ?? null,
            ]);

            return response()->json($order->fresh(), 201);
            });
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function myOrders(Request $req)
    {
        $limit = min(max((int) $req->input('limit', 10), 1), 50);
        $page = max((int) $req->input('page', 1), 1);
        $query = Order::query()->where('user_id', auth()->id())->latest();
        $total = $query->count();
        $orders = $query->skip(($page - 1) * $limit)->take($limit)->get();
        return response()->json([
            'orders' => $orders,
            'total' => $total,
            'page' => $page,
            'pages' => (int) ceil($total / $limit),
        ]);
    }

    public function show(Request $req, $id)
    {
        $order = Order::with('user')->find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        if ((int) $order->user_id !== (int) auth()->id() && (($req->user()->role ?? null) !== 'admin')) {
            return response()->json(['error' => 'Not authorized'], 403);
        }
        return response()->json($order);
    }

    public function index(Request $req)
    {
        $limit = min(max((int) $req->input('limit', 20), 1), 100);
        $page = max((int) $req->input('page', 1), 1);
        $query = Order::query()->latest();
        if ($req->filled('status')) {
            $query->where('status', $req->query('status'));
        }
        $total = $query->count();
        $orders = $query->skip(($page - 1) * $limit)->take($limit)->get();
        return response()->json([
            'orders' => $orders,
            'total' => $total,
            'page' => $page,
            'pages' => (int) ceil($total / $limit),
        ]);
    }

    // Order state machine — mirrors Node.js order.service.js
    // 'paid' is intentionally excluded from admin control — only the payment webhook may set it
    protected const STATE_MACHINE = [
        'pending'          => ['awaiting_payment', 'cancelled'],
        'awaiting_payment' => ['cancelled'],
        'paid'             => ['confirmed'],
        'confirmed'        => ['shipped', 'cancelled'],
        'shipped'          => ['delivered'],
        'delivered'        => [],
        'cancelled'        => [],
    ];

    public function updateStatus(Request $req, $id)
    {
        $data = $req->validate([
            'status' => ['required', 'in:awaiting_payment,confirmed,shipped,delivered,cancelled'],
        ]);

        $toStatus = $data['status'];

        // Block: 'paid' is set only by the payment gateway webhook
        if ($toStatus === 'paid') {
            return response()->json(['error' => 'Payment status is set by the payment gateway only'], 400);
        }

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $allowed = self::STATE_MACHINE[$order->status] ?? [];
        if (!in_array($toStatus, $allowed, true)) {
            return response()->json([
                'error'   => "Invalid transition: {$order->status} → {$toStatus}",
                'allowed' => $allowed,
            ], 400);
        }

        $order->status = $toStatus;
        $order->save();
        return response()->json($order);
    }

    public function tracking($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Tracking record not found'], 404);
        }

        $trackingStatus = match ($order->status) {
            'delivered' => 'delivered',
            'shipped' => 'in_transit',
            'cancelled' => 'cancelled',
            default => 'confirmed',
        };

        $updatedAt = $order->getRawOriginal('updated_at')
            ? $order->asDateTime($order->getRawOriginal('updated_at'))
            : now();

        return response()->json([
            'trackingId' => (string) $order->id,
            'orderId' => $order->id,
            'status' => $trackingStatus,
            'orderStatus' => $order->status,
            'lastUpdated' => $updatedAt->toIso8601String(),
            'eta' => $trackingStatus === 'delivered' || $trackingStatus === 'cancelled'
                ? null
                : $updatedAt->copy()->addDays(3)->toIso8601String(),
            'customer' => [
                'name' => $order->shippingAddress['name'] ?? null,
                'city' => $order->shippingAddress['city'] ?? null,
            ],
            'items' => collect($order->items ?? [])->map(fn ($item) => [
                'name' => $item['name'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
            ])->values(),
        ]);
    }
}
