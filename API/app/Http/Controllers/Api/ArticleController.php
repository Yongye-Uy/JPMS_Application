<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function index(Request $request)
    {
        return $this->relay($this->central->get('/articles', $request->query()));
    }

    public function show(int $id)
    {
        return $this->relay($this->central->get("/articles/{$id}"));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'page_start' => 'nullable|integer',
            'page_end' => 'nullable|integer',
        ]);

        return $this->relay($this->central->patch("/articles/{$id}", $data));
    }

    public function destroy(int $id)
    {
        return $this->relay($this->central->delete("/articles/{$id}"));
    }

    /**
     * Relays the PDF from Central-Service. Buffers the file in memory for
     * this hop (acceptable for the local-disk MVP's PDF sizes per the
     * plan's open decision #7) — swap to a presigned-URL redirect once
     * S3/R2 is configured instead of proxying bytes through every layer.
     */
    public function download(int $id)
    {
        $response = $this->central->get("/articles/{$id}/download");

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/pdf');
    }

    public function trackMetric(Request $request, int $id)
    {
        $data = $request->validate(['event' => 'required|string|in:view,download,citation']);

        return $this->relay($this->central->post("/articles/{$id}/metrics/track", $data));
    }

    public function todayMetrics(int $id)
    {
        return $this->relay($this->central->get("/articles/{$id}/metrics/today"));
    }
}
