<?php

use App\Http\Controllers\Reader\ArticleController;
use App\Http\Controllers\Reader\IssueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module 6 — Public reader access. Outside auth:remote-sanctum (see
| routes/api.php) — download enforces its own auth check.
|--------------------------------------------------------------------------
*/

Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{id}', [ArticleController::class, 'show']);
Route::get('articles/{id}/view', [ArticleController::class, 'view']);
Route::get('articles/{id}/download', [ArticleController::class, 'download']);
Route::post('articles/{id}/metrics/track', [ArticleController::class, 'trackView']);
Route::get('articles/{id}/metrics/today', [ArticleController::class, 'todayMetrics']);

Route::get('issues', [IssueController::class, 'index']);
Route::get('issues/{id}', [IssueController::class, 'show']);
