<?php

namespace App\Services;

use App\Models\GbpToken;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class GbpService
{
    const CACHE_TTL_MS = 30 * 60 * 1000;

    protected function createClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(env('GBP_CLIENT_ID'));
        $client->setClientSecret(env('GBP_CLIENT_SECRET'));
        $client->setRedirectUri(env('GBP_REDIRECT_URI'));
        $client->setScopes(['https://www.googleapis.com/auth/business.manage']);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        return $client;
    }

    protected function doc(): GbpToken
    {
        return GbpToken::firstOrCreate(['id' => 'singleton']);
    }

    public function getAuthUrl(): string
    {
        return $this->createClient()->createAuthUrl();
    }

    public function handleCallback(string $code): array
    {
        $client = $this->createClient();
        $token = $client->fetchAccessTokenWithAuthCode($code);
        if (!empty($token['error'])) {
            throw new \RuntimeException('GBP auth error: ' . ($token['error_description'] ?? $token['error']));
        }

        $doc = $this->doc();
        $doc->access_token = $token['access_token'] ?? null;
        $doc->refresh_token = $token['refresh_token'] ?? $doc->refresh_token;
        $doc->expiry_date = isset($token['created']) && isset($token['expires_in'])
            ? ((int) $token['created'] + (int) $token['expires_in'])
            : null;
        $doc->save();

        return ['ok' => true];
    }

    public function disconnect(): array
    {
        GbpToken::whereKey('singleton')->delete();
        return ['ok' => true];
    }

    public function getStatus(): array
    {
        $doc = $this->doc();
        return [
            'connected' => (bool) ($doc->access_token || $doc->refresh_token),
            'accountId' => $doc->account_id,
            'locationId' => $doc->location_id,
            'accountName' => $doc->account_name,
            'locationName' => $doc->location_name,
            'reviewsCachedAt' => optional($doc->reviews_cached_at)?->toIso8601String(),
            'businessInfoCachedAt' => optional($doc->business_info_cached_at)?->toIso8601String(),
        ];
    }

    public function dispatch(string $method, string $path, array $data = [])
    {
        $path = trim($path, '/');
        return match ($path) {
            'connect' => ['url' => $this->getAuthUrl()],
            'disconnect' => strtoupper($method) === 'DELETE' ? $this->disconnect() : ['error' => 'Method not allowed'],
            'status' => array_merge($this->getStatus(), [
                'placesConfigured' => (bool) (env('GOOGLE_PLACES_API_KEY') && env('GOOGLE_PLACE_ID')),
            ]),
            'reviews' => $this->getReviews(!empty($data['refresh']) && (string) $data['refresh'] === '1'),
            'business-info' => $this->getBusinessInfo(!empty($data['refresh']) && (string) $data['refresh'] === '1'),
            'posts' => strtoupper($method) === 'POST'
                ? $this->createPost($data)
                : ['posts' => $this->listPosts()],
            default => ['ok' => true, 'path' => $path, 'method' => $method],
        };
    }

    protected function placesFetch(): array
    {
        $apiKey = env('GOOGLE_PLACES_API_KEY');
        $placeId = env('GOOGLE_PLACE_ID');
        if (!$apiKey || !$placeId) {
            throw new \RuntimeException('PLACES_NOT_CONFIGURED');
        }

        $url = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . urlencode($placeId)
            . '&fields=name,rating,user_ratings_total,reviews,opening_hours,formatted_phone_number,website'
            . '&key=' . urlencode($apiKey);

        $json = Http::get($url)->json();
        if (($json['status'] ?? null) !== 'OK') {
            throw new \RuntimeException('Places API error: ' . ($json['status'] ?? 'unknown'));
        }
        return $json['result'] ?? [];
    }

    public function getReviews(bool $forceRefresh = false): array
    {
        $doc = $this->doc();
        $cacheAge = $doc->reviews_cached_at ? now()->diffInMilliseconds($doc->reviews_cached_at) : INF;
        if (!$forceRefresh && $doc->cached_reviews && $cacheAge < self::CACHE_TTL_MS) {
            return $doc->cached_reviews;
        }

        $place = $this->placesFetch();
        $reviews = collect($place['reviews'] ?? [])->map(function ($r) {
            return [
                'reviewer' => [
                    'displayName' => $r['author_name'] ?? null,
                    'profilePhotoUrl' => $r['profile_photo_url'] ?? null,
                ],
                'starRating' => match ((int) ($r['rating'] ?? 0)) {
                    1 => 'ONE',
                    2 => 'TWO',
                    3 => 'THREE',
                    4 => 'FOUR',
                    default => 'FIVE',
                },
                'comment' => $r['text'] ?? null,
                'createTime' => isset($r['time']) ? date(DATE_ATOM, (int) $r['time']) : null,
            ];
        })->values()->all();

        $data = [
            'reviews' => $reviews,
            'averageRating' => $place['rating'] ?? null,
            'totalReviewCount' => $place['user_ratings_total'] ?? 0,
            'source' => 'places',
        ];

        $doc->cached_reviews = $data;
        $doc->reviews_cached_at = now();
        $doc->cached_business_info = [
            'title' => $place['name'] ?? null,
            'phoneNumbers' => !empty($place['formatted_phone_number']) ? ['primaryPhone' => $place['formatted_phone_number']] : null,
            'websiteUri' => $place['website'] ?? null,
            'regularHours' => !empty($place['opening_hours']['weekday_text']) ? ['weekdayText' => $place['opening_hours']['weekday_text']] : null,
            'openInfo' => ['status' => !empty($place['opening_hours']['open_now']) ? 'OPEN' : 'CLOSED'],
            'source' => 'places',
        ];
        $doc->business_info_cached_at = now();
        $doc->save();

        return $data;
    }

    public function getBusinessInfo(bool $forceRefresh = false)
    {
        $doc = $this->doc();
        $cacheAge = $doc->business_info_cached_at ? now()->diffInMilliseconds($doc->business_info_cached_at) : INF;
        if (!$forceRefresh && $doc->cached_business_info && $cacheAge < self::CACHE_TTL_MS) {
            return $doc->cached_business_info;
        }

        $this->getReviews(true);
        $doc->refresh();
        return $doc->cached_business_info ?: ['notConnected' => true];
    }

    public function createPost(array $args)
    {
        $doc = $this->doc();
        $posts = $doc->cached_business_info['posts'] ?? [];
        $post = [
            'summary' => $args['summary'] ?? '',
            'callToAction' => $args['callToAction'] ?? null,
            'mediaUrl' => $args['mediaUrl'] ?? null,
            'createdAt' => now()->toIso8601String(),
        ];
        $posts[] = $post;
        $doc->cached_business_info = array_merge($doc->cached_business_info ?? [], ['posts' => $posts]);
        $doc->save();
        return $post;
    }

    public function listPosts(): array
    {
        $doc = $this->doc();
        return $doc->cached_business_info['posts'] ?? [];
    }
}
