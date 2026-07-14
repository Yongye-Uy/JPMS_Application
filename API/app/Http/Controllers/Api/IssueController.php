<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function index(Request $request)
    {
        return $this->relay($this->central->get('/issues', $request->query()));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'journal_id' => 'required|integer',
            'volume' => 'required|integer',
            'number' => 'required|integer',
            'year' => 'required|integer',
            'publication_date' => 'nullable|date',
        ]);

        return $this->relay($this->central->post('/issues', $data));
    }

    public function show(int $id)
    {
        return $this->relay($this->central->get("/issues/{$id}"));
    }

    public function addArticle(Request $request, int $issueId)
    {
        $data = $request->validate([
            'manuscript_id' => 'required|integer',
            'page_start' => 'nullable|integer',
            'page_end' => 'nullable|integer',
        ]);

        return $this->relay($this->central->post("/issues/{$issueId}/articles", $data));
    }

    public function publish(int $id)
    {
        return $this->relay($this->central->post("/issues/{$id}/publish"));
    }
}
