<?php

namespace App\Clients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * The only outbound HTTP client Frontend is allowed to use — every piece
 * of data comes from Backend. Implements the BFF pattern: the Backend
 * Sanctum token lives ONLY in this server's session store (SESSION_DRIVER
 * =file), never sent to the browser — the browser only ever holds the
 * opaque Laravel session cookie.
 */
class BackendClient
{
    public function token(): ?string
    {
        return Session::get('backend_token');
    }

    public function setToken(?string $token): void
    {
        if ($token) {
            Session::put('backend_token', $token);
        } else {
            Session::forget('backend_token');
        }
    }

    private function http(): PendingRequest
    {
        $client = Http::baseUrl(config('services.backend.url'))->acceptJson();

        if ($token = $this->token()) {
            $client = $client->withToken($token);
        }

        return $client;
    }

    public function get(string $uri, array $query = []): Response
    {
        return $this->http()->get($uri, $query);
    }

    public function post(string $uri, array $data = []): Response
    {
        return $this->http()->post($uri, $data);
    }

    public function postMultipart(string $uri, array $data, array $files): Response
    {
        $request = $this->http()->asMultipart();

        foreach ($files as $name => $file) {
            if (is_array($file)) {
                foreach ($file as $f) {
                    $request = $request->attach($name.'[]', fopen($f->getRealPath(), 'r'), $f->getClientOriginalName());
                }
            } else {
                $request = $request->attach($name, fopen($file->getRealPath(), 'r'), $file->getClientOriginalName());
            }
        }

        foreach ($this->flatten($data) as $key => $value) {
            $request = $request->attach($key, (string) $value);
        }

        return $request->post($uri);
    }

    public function patch(string $uri, array $data = []): Response
    {
        return $this->http()->patch($uri, $data);
    }

    public function delete(string $uri, array $query = []): Response
    {
        return $this->http()->delete($uri, $query);
    }

    private function flatten(array $data, string $prefix = ''): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $field = $prefix === '' ? $key : "{$prefix}[{$key}]";

            if (is_array($value)) {
                $result += $this->flatten($value, $field);
            } else {
                $result[$field] = $value;
            }
        }

        return $result;
    }
}
