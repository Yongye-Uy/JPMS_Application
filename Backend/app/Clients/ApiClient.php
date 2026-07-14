<?php

namespace App\Clients;

use App\Support\CurrentActor;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * The only outbound HTTP client Backend is allowed to use — every
 * persistence operation goes through here to API. Forwards X-Actor-User-Id
 * (the currently authenticated RemoteUser, if any) so Central-Service's
 * audit log records who performed the action end-to-end.
 */
class ApiClient
{
    private function http(): PendingRequest
    {
        $client = Http::baseUrl(config('services.api.url'))
            ->withHeaders(['X-Internal-Token' => config('services.api.token')])
            ->acceptJson();

        if ($actorId = CurrentActor::id()) {
            $client = $client->withHeaders(['X-Actor-User-Id' => $actorId]);
        }

        return $client;
    }

    public function get(string $uri, array $query = []): Response
    {
        return $this->http()->get("/v1{$uri}", $query);
    }

    public function post(string $uri, array $data = []): Response
    {
        return $this->http()->post("/v1{$uri}", $data);
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

        return $request->post("/v1{$uri}");
    }

    public function patch(string $uri, array $data = []): Response
    {
        return $this->http()->patch("/v1{$uri}", $data);
    }

    public function delete(string $uri, array $query = []): Response
    {
        return $this->http()->delete("/v1{$uri}", $query);
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
