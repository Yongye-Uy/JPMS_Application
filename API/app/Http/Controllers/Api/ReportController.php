<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function journalPerformance(Request $request)
    {
        return $this->relay($this->central->get('/reports/journal-performance', $request->query()));
    }

    public function reviewerPerformance(Request $request)
    {
        return $this->relay($this->central->get('/reports/reviewer-performance', $request->query()));
    }
}
