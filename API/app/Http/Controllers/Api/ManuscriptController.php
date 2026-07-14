<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManuscriptController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function index(Request $request)
    {
        return $this->relay($this->central->get('/manuscripts', $request->query()));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'journal_id' => 'required|integer',
            'author_id' => 'required|integer',
            'manuscript_type' => 'required|string',
            'title' => 'required|string',
            'abstract' => 'nullable|string',
            'keywords' => 'sometimes|array',
        ]);

        return $this->relay($this->central->post('/manuscripts', $data));
    }

    public function show(int $id)
    {
        return $this->relay($this->central->get("/manuscripts/{$id}"));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'abstract' => 'nullable|string',
            'manuscript_type' => 'sometimes|string',
            'status' => 'sometimes|string',
        ]);

        return $this->relay($this->central->patch("/manuscripts/{$id}", $data));
    }

    /** Handles both "upload manuscript files" and "submit revised version" (module 2 functions 3 + 6). */
    public function storeVersion(Request $request, int $id)
    {
        $request->validate([
            'uploaded_by' => 'required|integer',
            'response_note' => 'nullable|string',
            'main_file' => 'required|file|mimes:pdf',
            'supplementary_files' => 'sometimes|array',
        ]);

        $files = ['main_file' => $request->file('main_file')];
        if ($request->hasFile('supplementary_files')) {
            $files['supplementary_files'] = $request->file('supplementary_files');
        }

        $data = $request->only(['uploaded_by', 'response_note']);

        return $this->relay($this->central->postMultipart("/manuscripts/{$id}/versions", $data, $files));
    }

    /** Relays the file bytes from Central-Service (same buffered-relay pattern as ArticleController::download()). */
    public function downloadFile(int $manuscriptId, int $fileId)
    {
        $response = $this->central->get("/manuscripts/{$manuscriptId}/files/{$fileId}/download");

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/pdf');
    }

    public function setMainVersion(int $id, int $versionId)
    {
        return $this->relay($this->central->post("/manuscripts/{$id}/versions/{$versionId}/set-main"));
    }

    public function inviteCoAuthor(Request $request, int $id)
    {
        $data = $request->validate([
            'inviting_author_id' => 'required|integer',
            'invited_author_id' => 'required|integer',
        ]);

        return $this->relay($this->central->post("/manuscripts/{$id}/co-authors/invite", $data));
    }

    public function submit(int $id)
    {
        return $this->relay($this->central->post("/manuscripts/{$id}/submit"));
    }

    public function withdraw(Request $request, int $id)
    {
        $data = $request->validate(['reason' => 'required|string']);

        return $this->relay($this->central->post("/manuscripts/{$id}/withdraw", $data));
    }

    public function resubmit(int $id)
    {
        return $this->relay($this->central->post("/manuscripts/{$id}/resubmit"));
    }

    public function destroy(int $id)
    {
        return $this->relay($this->central->delete("/manuscripts/{$id}"));
    }

    public function archive(int $id)
    {
        return $this->relay($this->central->post("/manuscripts/{$id}/archive"));
    }

    public function restore(int $id)
    {
        return $this->relay($this->central->post("/manuscripts/{$id}/restore"));
    }
}
