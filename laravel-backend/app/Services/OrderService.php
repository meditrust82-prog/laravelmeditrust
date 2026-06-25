<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public static function audit($event, $id, $meta = [])
    {
        Log::info('order.audit', array_merge(['event'=>$event,'id'=>$id], (array)$meta));
    }

    public static function markPaidByGateway($orderId, $info = [])
    {
        return DB::transaction(function () use ($orderId, $info) {
            $order = Order::lockForUpdate()->find($orderId);
            if (!$order) return null;
            if (!empty($order->payment_webhook_event_id) || in_array($order->status, ['paid','cancelled'])) {
                return $order;
            }
            $order->payment_webhook_event_id = $info['pidx'] ?? ($info['paymentId'] ?? null);
            $order->status = 'paid';
            $order->payment_gateway = $info['gateway'] ?? $order->payment_gateway;
            $order->payment_id = $info['paymentId'] ?? $order->payment_id;
            $order->payment_paid_at = now();
            $order->save();
            self::audit('order.paid', $orderId, $info);
            return $order;
        });
    }

    public static function markCancelledByGateway($orderId, $info = [])
    {
        return DB::transaction(function () use ($orderId, $info) {
            $order = Order::lockForUpdate()->find($orderId);
            if (!$order) return null;
            $order->status = 'cancelled';
            $order->payment_webhook_event_id = $order->payment_webhook_event_id ?: ($info['pidx'] ?? null);
            $order->save();
            self::audit('order.cancelled', $orderId, $info);
            return $order;
        });
    }
}
