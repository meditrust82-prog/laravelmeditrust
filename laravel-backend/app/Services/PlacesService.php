<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\GbpToken;

class PlacesService
{
    const CACHE_TTL_MS = 60 * 60 * 1000; // 1 hour

    protected function fetchJson($url)
    {
        $resp = Http::get($url);
        if (!$resp->ok()) throw new \Exception('Places API fetch failed');
        return $resp->json();
    }

    public function getPlacesReviews($forceRefresh = false)
    {
        $apiKey = env('GOOGLE_PLACES_API_KEY');
        $placeId = env('GOOGLE_PLACE_ID');
        if (!$apiKey || !$placeId) throw new \Exception('PLACES_NOT_CONFIGURED');

        $doc = GbpToken::find('singleton');
        $cacheAge = $doc && $doc->reviewsCachedAt ? (time()*1000 - strtotime($doc->reviewsCachedAt)*1000) : INF;
        if (!$forceRefresh && $doc && !empty($doc->cachedReviews) && $cacheAge < self::CACHE_TTL_MS) {
            return $doc->cachedReviews;
        }

        $fields = 'name,rating,user_ratings_total,reviews,opening_hours,formatted_phone_number,website';
        $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$placeId}&fields={$fields}&key={$apiKey}";
        $json = $this->fetchJson($url);
        if (($json['status'] ?? null) !== 'OK') {
            throw new \Exception('Places API error: ' . ($json['status'] ?? 'UNKNOWN'));
        }
        $place = $json['result'] ?? [];
        $starMap = [1=>'ONE',2=>'TWO',3=>'THREE',4=>'FOUR',5=>'FIVE'];
        $reviews = [];
        foreach (($place['reviews'] ?? []) as $r) {
            $reviews[] = [
                'reviewer' => [
                    'displayName' => $r['author_name'] ?? null,
                    'profilePhotoUrl' => $r['profile_photo_url'] ?? null,
                ],
                'starRating' => $starMap[$r['rating']] ?? 'FIVE',
                'comment' => $r['text'] ?? null,
                'createTime' => isset($r['time']) ? date('c', $r['time']) : null,
            ];
        }
        $data = [
            'reviews' => $reviews,
            'averageRating' => $place['rating'] ?? null,
            'totalReviewCount' => $place['user_ratings_total'] ?? 0,
            'source' => 'places',
        ];
        GbpToken::updateOrCreate(['id'=>'singleton'], ['cachedReviews'=>$data, 'reviewsCachedAt'=>now()]);

        $businessInfo = [
            'title' => $place['name'] ?? null,
            'phoneNumbers' => $place['formatted_phone_number'] ? ['primaryPhone'=>$place['formatted_phone_number']] : null,
            'websiteUri' => $place['website'] ?? null,
            'regularHours' => isset($place['opening_hours']['weekday_text']) ? ['weekdayText'=>$place['opening_hours']['weekday_text']] : null,
            'openInfo' => ['status' => ($place['opening_hours']['open_now'] ?? false) ? 'OPEN' : 'CLOSED'],
            'source' => 'places',
        ];
        GbpToken::updateOrCreate(['id'=>'singleton'], ['cachedBusinessInfo'=>$businessInfo, 'businessInfoCachedAt'=>now()]);

        return $data;
    }

    public function getPlacesBusinessInfo($forceRefresh = false)
    {
        $doc = GbpToken::find('singleton');
        $cacheAge = $doc && $doc->businessInfoCachedAt ? (time()*1000 - strtotime($doc->businessInfoCachedAt)*1000) : INF;
        if (!$forceRefresh && $doc && !empty($doc->cachedBusinessInfo) && $cacheAge < self::CACHE_TTL_MS) {
            return $doc->cachedBusinessInfo;
        }
        // trigger fetch
        $this->getPlacesReviews(true);
        $fresh = GbpToken::find('singleton');
        return $fresh->cachedBusinessInfo ?? null;
    }
}
