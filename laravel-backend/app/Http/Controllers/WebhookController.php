<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Services\OrderService;

class WebhookController extends Controller
{
    public function khalti(Request $req)
    {
        try {
            $secret = env('KHALTI_SECRET_KEY');
            if (!$secret) return response()->json(['error'=>'Khalti not configured'],503);

            // IP allowlist
            $allowed = array_filter(array_map('trim', explode(',', env('KHALTI_ALLOWED_IPS', ''))));
            $ip = $req->ip();
            if (!empty($allowed) && !in_array($ip, $allowed)) {
                OrderService::audit('webhook.blocked','unknown',['gateway'=>'khalti','ip'=>$ip]);
                return response()->json(['error'=>'Forbidden'],403);
            }

            // Optional HMAC signature verification (if sender provides header)
            $body = $req->getContent();
            $sigHeader = $req->header('X-KHALTI-SIGNATURE') ?? $req->header('X-Khalti-Signature');
            if ($sigHeader) {
                $computed = hash_hmac('sha256', $body, $secret);
                if (!hash_equals($computed, $sigHeader)) {
                    OrderService::audit('webhook.signature_mismatch','unknown',['gateway'=>'khalti','ip'=>$ip]);
                    return response()->json(['error'=>'Invalid signature'],403);
                }
            }

            $pidx = $req->input('pidx');
            $orderId = $req->input('purchase_order_id');
            $callbackStatus = $req->input('status');

            if (!$pidx || !$orderId || !$callbackStatus) {
                return response()->json(['error'=>'Missing fields: pidx, purchase_order_id, status'],400);
            }
            if (!is_string($pidx) || !preg_match('/^[a-zA-Z0-9_-]{1,100}$/', $pidx)) {
                return response()->json(['error'=>'Invalid pidx format'],400);
            }
            if (!is_string($orderId) || !preg_match('/^[0-9]+$/', $orderId) && !preg_match('/^[a-f0-9]{24}$/i', $orderId)) {
                // allow numeric or mongodb-like hex id fallback
                return response()->json(['error'=>'Invalid purchase_order_id format'],400);
            }
            if (!is_string($callbackStatus) || strlen($callbackStatus) > 50) {
                return response()->json(['error'=>'Invalid status field'],400);
            }

            $order = Order::find($orderId);
            if (!$order) return response()->json(['error'=>'Order not found'],404);

            if (!empty($order->payment_webhook_event_id) || in_array($order->status, ['paid','cancelled'])) {
                return response()->json(['received'=>true,'idempotent'=>true]);
            }

            // Server-to-server verification with Khalti
            $timeoutSeconds = 8;
            try {
                $resp = Http::timeout($timeoutSeconds)
                    ->withHeaders([
                        'Authorization' => 'Key ' . $secret,
                        'Content-Type' => 'application/json',
                    ])->post('https://khalti.com/api/v2/epayment/lookup/', ['pidx'=>$pidx]);
            } catch (\Exception $e) {
                OrderService::audit('webhook.verify_failed', $orderId, ['gateway'=>'khalti','pidx'=>$pidx,'error'=>$e->getMessage()]);
                return response()->json(['error'=>'Payment verification failed. Will retry.'],502);
            }

            if (!$resp->ok()) {
                $body = $resp->body();
                OrderService::audit('webhook.verify_failed', $orderId, ['gateway'=>'khalti','pidx'=>$pidx,'http_status'=>$resp->status(),'body'=>$body]);
                return response()->json(['error'=>'Payment verification failed. Will retry.'],502);
            }

            $verification = $resp->json();
            if (($verification['status'] ?? null) !== 'Completed') {
                OrderService::markCancelledByGateway($orderId, ['pidx'=>$pidx,'gateway'=>'khalti','reason'=>$verification['status'] ?? 'unknown']);
                return response()->json(['received'=>true]);
            }

            $expectedPaisa = (int) round((($order->total_price ?? 0)) * 100);
            $receivedPaisa = isset($verification['total_amount']) ? (int) $verification['total_amount'] : null;
            if (!is_int($receivedPaisa) || $receivedPaisa !== $expectedPaisa) {
                OrderService::audit('webhook.amount_mismatch', $orderId, ['gateway'=>'khalti','pidx'=>$pidx,'expected'=>$expectedPaisa,'received'=>$receivedPaisa]);
                OrderService::markCancelledByGateway($orderId, ['pidx'=>$pidx,'gateway'=>'khalti','reason'=>'amount_mismatch']);
                return response()->json(['error'=>'Payment amount mismatch'],400);
            }

            OrderService::markPaidByGateway($orderId, ['pidx'=>$pidx,'paymentId'=>$verification['pidx'] ?? null,'gateway'=>'khalti']);

            return response()->json(['received'=>true]);

        } catch (\Exception $err) {
            OrderService::audit('webhook.error','unknown',['gateway'=>'khalti','error'=>$err->getMessage()]);
            return response()->json(['error'=>'Internal error. Will retry.'],500);
        }
    }
}
