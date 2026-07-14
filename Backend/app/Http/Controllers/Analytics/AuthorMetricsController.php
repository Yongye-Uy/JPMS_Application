<?php

namespace App\Http\Controllers\Analytics;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorMetricsController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /**
     * Module 7 function 3: Author's own published articles, including
     * view/download/citation totals (Central-Service's `GET /articles`
     * eager-sums `article_metrics` per article via `withSum`).
     */
    public function index(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $query = $request->query();
        $query['include_unpublished'] = true;

        $response = $this->api->get('/articles', $query);

        if (! $response->successful()) {
            return response()->json($response->json(), $response->status());
        }

        $articles = collect($response->json('data') ?? $response->json())
            ->filter(fn ($article) => ($article['manuscript']['author_id'] ?? null) === $user->id)
            ->values();

        return response()->json(['articles' => $articles]);
    }
}
