<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function index(Request $request)
    {
        return $this->relay($this->central->get('/audit-log', $request->query()));
    }
}
