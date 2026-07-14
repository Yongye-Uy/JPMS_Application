<?php

namespace App\Http\Controllers;

use App\Support\AuthenticatedUser;

class DashboardHomeController extends Controller
{
    /** Redirects to the highest-priority role's dashboard (Admin > Editor > Reviewer > Author > Reader). */
    public function __invoke()
    {
        return match (AuthenticatedUser::primaryRole()) {
            'Admin' => redirect()->route('admin.dashboard'),
            'Editor' => redirect()->route('editor.dashboard'),
            'Reviewer' => redirect()->route('reviewer.dashboard'),
            'Author' => redirect()->route('author.dashboard'),
            'Reader' => redirect()->route('reader.dashboard'),
            default => redirect()->route('public.home'),
        };
    }
}
