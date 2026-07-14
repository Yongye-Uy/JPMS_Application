<?php

namespace App\Http\Controllers;

use App\Clients\BackendClient;

/**
 * Streams a PDF from Backend (which relays from API -> Central-Service).
 * Used for the native <embed type="application/pdf"> viewers throughout
 * the dashboard — e.g. src="{{ route('files.show', ['path' => 'articles/5/download']) }}".
 */
class FileProxyController extends Controller
{
    public function __invoke(string $path, BackendClient $backend)
    {
        $response = $backend->get("/{$path}");

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/pdf')
            ->header('Content-Disposition', $response->header('Content-Disposition') ?: 'inline');
    }
}
