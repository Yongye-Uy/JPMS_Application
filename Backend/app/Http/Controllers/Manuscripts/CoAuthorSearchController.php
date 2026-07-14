<?php

namespace App\Http\Controllers\Manuscripts;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoAuthorSearchController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /**
     * Module 2 function 4: search existing users to invite as a co-author.
     * Open to any authenticated user (unlike Admin\UserController::index,
     * which is the full user-management listing) — same relay pattern as
     * Reviews\ReviewerController::index.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $query = $request->query();
        $query['role'] = 'Author';
        $query['per_page'] = $query['per_page'] ?? 10;
        $query['exclude_id'] = $user->id;

        $response = $this->api->get('/users', $query);

        return response()->json($response->json(), $response->status());
    }
}
