<?php

use App\Http\Controllers\Publication\ArticleController;
use App\Http\Controllers\Publication\IssueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module 5 — Production & publication.
|--------------------------------------------------------------------------
*/

// NOTE: GET issues / GET issues/{id} intentionally live only in
// routes/modules/reader.php (not here) to avoid registering the same URI
// twice — that public controller is role-aware: Editor/Admin callers see
// Draft issues too, everyone else only sees Published ones. See
// App\Http\Controllers\Reader\IssueController.
Route::middleware('role:Editor,Admin')->group(function () {
    Route::post('issues', [IssueController::class, 'store']);
    Route::post('issues/{issueId}/articles', [IssueController::class, 'addArticle']);
    Route::post('issues/{id}/publish', [IssueController::class, 'publish']);

    Route::patch('articles/{id}', [ArticleController::class, 'update']);
    Route::delete('articles/{id}', [ArticleController::class, 'destroy']);
});
