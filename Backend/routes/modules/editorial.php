<?php

use App\Http\Controllers\Editorial\EditorAssignmentController;
use App\Http\Controllers\Editorial\EditorialDecisionController;
use App\Http\Controllers\Editorial\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module 4 — Editorial workflow (assignment, decisions, messaging).
|--------------------------------------------------------------------------
*/

Route::middleware('role:Editor,Admin')->group(function () {
    Route::post('manuscripts/{manuscriptId}/editor-assignments', [EditorAssignmentController::class, 'store']);

    // Also used for desk-reject/screening decisions — same endpoint, different `decision` value.
    Route::post('manuscripts/{manuscriptId}/decisions', [EditorialDecisionController::class, 'store']);

    Route::get('manuscripts/{manuscriptId}/messages', [MessageController::class, 'index']);
    Route::post('manuscripts/{manuscriptId}/messages', [MessageController::class, 'store']);
    Route::post('messages/{id}/read', [MessageController::class, 'markRead']);

    // Reassign reviewer = invite reviewer again; handled by
    // routes/modules/reviews.php's review-invitations endpoint (no duplicate route here).
});
