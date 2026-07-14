<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function store(Request $request, int $invitationId)
    {
        $request->validate([
            'recommendation' => 'required|string',
            'comments_to_author' => 'required|string',
            'comments_to_editor' => 'nullable|string',
            'scores' => 'required|array',
            'scores.*.criterion' => 'required|string',
            'scores.*.score' => 'required|integer|min:0|max:5',
            'annotated_file' => 'sometimes|file',
        ]);

        $data = $request->except('annotated_file');

        $files = $request->hasFile('annotated_file') ? ['annotated_file' => $request->file('annotated_file')] : [];

        return $this->relay($this->central->postMultipart("/review-invitations/{$invitationId}/reviews", $data, $files));
    }

    public function show(int $id)
    {
        return $this->relay($this->central->get("/reviews/{$id}"));
    }

    public function downloadFile(int $reviewId, int $fileId)
    {
        $response = $this->central->get("/reviews/{$reviewId}/files/{$fileId}/download");

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/pdf');
    }
}
