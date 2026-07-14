<?php

namespace App\Http\Controllers\Analytics;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** Module 7 function 1: Journal performance report. */
    public function journalPerformance(Request $request)
    {
        $response = $this->api->get('/reports/journal-performance', $request->query());

        return response()->json($response->json(), $response->status());
    }

    /** Module 7 function 2: Reviewer performance report. */
    public function reviewerPerformance(Request $request)
    {
        $response = $this->api->get('/reports/reviewer-performance', $request->query());

        return response()->json($response->json(), $response->status());
    }
}
