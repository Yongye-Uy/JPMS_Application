<?php

namespace App\Http\Controllers\Reviews;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewerController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** Module 3 function 1: Search reviewers (editor-only). */
    public function index(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $query = $request->query();
        $query['role'] = 'Reviewer';
        $query['per_page'] = $query['per_page'] ?? 10;
        $query['exclude_id'] = $user->id;

        $response = $this->api->get('/users', $query);

        return response()->json($response->json(), $response->status());
    }
}
