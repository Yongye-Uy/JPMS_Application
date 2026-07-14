<?php

use App\Http\Controllers\Analytics\AuthorMetricsController;
use App\Http\Controllers\Analytics\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module 7 — Analytics & reporting.
|--------------------------------------------------------------------------
*/

Route::middleware('role:Editor,Admin')->group(function () {
    Route::get('reports/journal-performance', [ReportController::class, 'journalPerformance']);
    Route::get('reports/reviewer-performance', [ReportController::class, 'reviewerPerformance']);
});

// Any authenticated author may view their own article list — self-scoped by user id, no role gate needed.
Route::get('author/metrics', [AuthorMetricsController::class, 'index']);
