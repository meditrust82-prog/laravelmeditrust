<?php

namespace App\Http\Controllers;

use App\Services\GbpService;
use Illuminate\Http\Request;

class GbpController extends Controller
{
    public function __construct(protected GbpService $service)
    {
    }

    public function connect()
    {
        return response()->json(['url' => $this->service->getAuthUrl()]);
    }

    public function callback(Request $req)
    {
        $code = $req->query('code');
        if (!$code) {
            return response()->json(['error' => 'Missing code'], 400);
        }
        try {
            $this->service->handleCallback($code);
            return redirect()->away((env('FRONTEND_URL') ?: env('APP_URL')) . '/admin/dashboard?gbp=connected');
        } catch (\Throwable $e) {
            return redirect()->away((env('FRONTEND_URL') ?: env('APP_URL')) . '/admin/dashboard?gbp=error&msg=' . urlencode($e->getMessage()));
        }
    }

    public function disconnect()
    {
        return response()->json($this->service->disconnect());
    }

    public function status()
    {
        $status = $this->service->getStatus();
        $status['placesConfigured'] = (bool) (env('GOOGLE_PLACES_API_KEY') && env('GOOGLE_PLACE_ID'));
        return response()->json($status);
    }

    public function reviews(Request $req)
    {
        try {
            return response()->json($this->service->getReviews($req->query('refresh') === '1'));
        } catch (\Throwable $e) {
            if ($e->getMessage() === 'PLACES_NOT_CONFIGURED') {
                return response()->json(['reviews' => [], 'averageRating' => null, 'totalReviewCount' => 0, 'notConnected' => true]);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function businessInfo(Request $req)
    {
        try {
            return response()->json($this->service->getBusinessInfo($req->query('refresh') === '1'));
        } catch (\Throwable $e) {
            if ($e->getMessage() === 'PLACES_NOT_CONFIGURED') {
                return response()->json(['notConnected' => true]);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function posts(Request $req)
    {
        if ($req->isMethod('post')) {
            $data = $req->validate(['summary' => ['required', 'string']]);
            return response()->json(['ok' => true, 'post' => $this->service->createPost([
                'summary' => $data['summary'],
                'callToAction' => $req->input('callToAction'),
                'mediaUrl' => $req->input('mediaUrl'),
            ])]);
        }
        return response()->json(['posts' => $this->service->listPosts()]);
    }
}
