<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /** Relays a Central-Service response unchanged — same status code and JSON body. */
    protected function relay(Response $response): JsonResponse
    {
        return response()->json($response->json(), $response->status());
    }
}
