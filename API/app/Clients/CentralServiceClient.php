<?php

namespace App\Clients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request as RequestFacade;

/**
 * The only outbound HTTP client API is allowed to use — every persistence
 * operation goes through here to Central-Service's /internal/v1/* routes.
 * Forwards X-Actor-User-Id (set by Backend on the inbound request) so
 * Central-Service's audit log records who performed the action.
 */
class CentralServiceClient
{
    private function http(): PendingRequest
    {
        $client = Http::baseUrl(config('services.central_service.url'))
            ->withHeaders(['X-Internal-Token' => config('services.central_service.token')])
            ->acceptJson();

        if ($actorId = RequestFacade::header('X-Actor-User-Id')) {
            $client = $client->withHeaders(['X-Actor-User-Id' => $actorId]);
        }

        return $client;
    }

    public function get(string $uri, array $query = []): Response
    {
        return $this->http()->get("/internal/v1{$uri}", $query);
    }

    public function post(string $uri, array $data = []): Response
    {
        return $this->http()->post("/internal/v1{$uri}", $data);
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

        return $request->post("/internal/v1{$uri}");
    }

    /**
     * Flattens nested arrays into PHP's multipart bracket notation
     * (scores[0][criterion]=X) so Central-Service's Validator sees a real
     * array for fields like "scores", not a scalar string.
     */
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

    public function patch(string $uri, array $data = []): Response
    {
        return $this->http()->patch("/internal/v1{$uri}", $data);
    }

    public function delete(string $uri, array $query = []): Response
    {
        return $this->http()->delete("/internal/v1{$uri}", $query);
    }
}
