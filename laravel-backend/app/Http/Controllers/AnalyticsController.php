<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class AnalyticsController extends Controller
{
    public function index(Request $req)
    {
        $orders = Order::latest()->get();
        $productIds = $orders->flatMap(fn ($order) => collect($order->items ?? [])->pluck('product'))->unique()->filter()->values()->all();
        $costMap = Product::whereIn('id', $productIds)->pluck('cost', 'id')->toArray();

        $totalRevenue = 0;
        $totalCost = 0;
        $productStats = [];

        foreach ($orders as $order) {
            $orderRevenue = (float) ($order->total_price ?? 0);
            $orderCost = 0;
            foreach ($order->items ?? [] as $item) {
                $pid = (string) ($item['product'] ?? '');
                $itemCost = (float) ($item['cost'] ?? ($costMap[$pid] ?? 0));
                $itemRevenue = (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0);
                $itemTotalCost = $itemCost * (int) ($item['quantity'] ?? 0);
                $orderCost += $itemTotalCost;

                if (!isset($productStats[$pid])) {
                    $productStats[$pid] = [
                        'name' => $item['name'] ?? '',
                        'sold' => 0,
                        'revenue' => 0,
                        'cost' => 0,
                        'profit' => 0,
                    ];
                }
                $productStats[$pid]['sold'] += (int) ($item['quantity'] ?? 0);
                $productStats[$pid]['revenue'] += $itemRevenue;
                $productStats[$pid]['cost'] += $itemTotalCost;
                $productStats[$pid]['profit'] += $itemRevenue - $itemTotalCost;
            }
            $totalRevenue += $orderRevenue;
            $totalCost += $orderCost;
        }

        $topProducts = collect($productStats)->sortByDesc('profit')->take(10)->values();

        return response()->json([
            'summary' => [
                'totalOrders' => $orders->count(),
                'totalRevenue' => $totalRevenue,
                'totalCost' => $totalCost,
                'totalProfit' => $totalRevenue - $totalCost,
                'profitMargin' => $totalRevenue > 0 ? number_format((($totalRevenue - $totalCost) / $totalRevenue) * 100, 1) : 0,
            ],
            'topProducts' => $topProducts,
            'recentOrders' => $orders->take(10)->map(fn ($order) => [
                'id' => $order->id,
                'total' => $order->total_price,
                'status' => $order->status,
                'date' => $order->createdAt,
            ])->values(),
        ]);
    }
}
