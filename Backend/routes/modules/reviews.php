<?php

use App\Http\Controllers\Reviews\ReviewController;
use App\Http\Controllers\Reviews\ReviewerController;
use App\Http\Controllers\Reviews\ReviewInvitationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module 3 — Peer review workflow.
|--------------------------------------------------------------------------
*/

// Editor-only: search reviewers, invite, decide extension requests.
Route::middleware('role:Editor,Admin')->group(function () {
    Route::get('reviewers', [ReviewerController::class, 'index']);
    Route::post('manuscripts/{manuscriptId}/review-invitations', [ReviewInvitationController::class, 'store']);
    Route::post('review-invitations/{id}/decide-extension', [ReviewInvitationController::class, 'decideExtension']);
});

// Reviewer-only: respond to invitation, request extension, submit review.
Route::middleware('role:Reviewer')->group(function () {
    Route::post('review-invitations/{id}/respond', [ReviewInvitationController::class, 'respond']);
    Route::post('review-invitations/{id}/request-extension', [ReviewInvitationController::class, 'requestExtension']);
    Route::post('review-invitations/{invitationId}/reviews', [ReviewController::class, 'store']);
});

// Either party may list/view invitations and submitted reviews — ownership
// is enforced inside the controller (a Reviewer only ever sees their own).
Route::middleware('role:Reviewer,Editor,Admin')->group(function () {
    Route::get('review-invitations', [ReviewInvitationController::class, 'index']);
    Route::get('review-invitations/{id}', [ReviewInvitationController::class, 'show']);
    Route::get('reviews/{id}', [ReviewController::class, 'show']);
    Route::get('reviews/{reviewId}/files/{fileId}/download', [ReviewController::class, 'downloadFile']);
});
