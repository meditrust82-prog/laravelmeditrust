<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;

class HealthController extends Controller
{
    /**
     * Simple health check endpoint.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        return FacadeResponse::make('OK', 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * API health check endpoint.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function api()
    {
        return response()->json(['status' => 'ok', 'version' => 'v1']);
    }

}

