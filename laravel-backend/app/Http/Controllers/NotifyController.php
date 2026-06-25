<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotifyController extends Controller
{
    protected function sendTelegram(string $text): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        if (!$token || !$chatId) {
            return;
        }

        Http::post('https://api.telegram.org/bot' . $token . '/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public function newsletter(Request $req)
    {
        $data = $req->validate([
            'name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'source' => ['nullable', 'string'],
        ]);

        Subscriber::updateOrCreate(
            ['phone' => $data['phone']],
            ['name' => $data['name'], 'source' => $data['source'] ?? 'home_newsletter', 'active' => true]
        );

        $this->sendTelegram("📬 <b>Newsletter Signup</b>\n👤 Name: {$data['name']}\n📞 Phone: {$data['phone']}\n🌐 Source: meditrustnepal.com");

        return response()->json(['ok' => true]);
    }

    public function subscribers(Request $req)
    {
        $limit = min(max((int) $req->input('limit', 100), 1), 1000);
        $page = max((int) $req->input('page', 1), 1);
        $query = Subscriber::query()->latest();
        $total = $query->count();
        $subscribers = $query->skip(($page - 1) * $limit)->take($limit)->get();
        return response()->json(['subscribers' => $subscribers, 'total' => $total]);
    }

    public function destroySubscriber($id)
    {
        Subscriber::whereKey($id)->delete();
        return response()->json(['ok' => true]);
    }

    public function quote(Request $req)
    {
        $data = $req->validate([
            'name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'product' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
        ]);

        $msg = "🏥 <b>New Quote Request</b>\n👤 Name: {$data['name']}\n📞 Phone: {$data['phone']}";
        if (!empty($data['product'])) {
            $msg .= "\n📦 Product: {$data['product']}";
        }
        if (!empty($data['message'])) {
            $msg .= "\n💬 Message: {$data['message']}";
        }
        $msg .= "\n🌐 Source: meditrustnepal.com";

        $this->sendTelegram($msg);

        return response()->json(['ok' => true]);
    }
}
