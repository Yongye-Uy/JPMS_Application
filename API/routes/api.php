<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CoAuthorInvitationController;
use App\Http\Controllers\Api\EditorAssignmentController;
use App\Http\Controllers\Api\EditorialDecisionController;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\ManuscriptController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ReviewInvitationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public data contract (only Backend may call these — see backend.token)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware(['backend.token'])->group(function () {

    Route::post('auth/verify-credentials', [AuthController::class, 'verifyCredentials']);
    Route::post('auth/tokens', [AuthController::class, 'issueToken']);
    Route::post('auth/tokens/introspect', [AuthController::class, 'introspectToken']);
    Route::delete('auth/tokens/{tokenId}', [AuthController::class, 'revokeToken']);

    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::patch('users/{id}', [UserController::class, 'update']);
    Route::post('users/{id}/roles', [UserController::class, 'assignRole']);
    Route::delete('users/{id}/roles/{roleId}', [UserController::class, 'revokeRole']);

    Route::get('journals', [JournalController::class, 'index']);
    Route::post('journals', [JournalController::class, 'store']);
    Route::get('journals/{id}', [JournalController::class, 'show']);
    Route::patch('journals/{id}', [JournalController::class, 'update']);
    Route::post('journals/{id}/archive', [JournalController::class, 'archive']);
    Route::post('journals/{id}/restore', [JournalController::class, 'restore']);

    Route::get('manuscripts', [ManuscriptController::class, 'index']);
    Route::post('manuscripts', [ManuscriptController::class, 'store']);
    Route::get('manuscripts/{id}', [ManuscriptController::class, 'show']);
    Route::patch('manuscripts/{id}', [ManuscriptController::class, 'update']);
    Route::delete('manuscripts/{id}', [ManuscriptController::class, 'destroy']);
    Route::post('manuscripts/{id}/versions', [ManuscriptController::class, 'storeVersion']);
    Route::get('manuscripts/{manuscriptId}/files/{fileId}/download', [ManuscriptController::class, 'downloadFile']);
    Route::post('manuscripts/{id}/versions/{versionId}/set-main', [ManuscriptController::class, 'setMainVersion']);
    Route::post('manuscripts/{id}/co-authors/invite', [ManuscriptController::class, 'inviteCoAuthor']);
    Route::post('manuscripts/{id}/submit', [ManuscriptController::class, 'submit']);
    Route::post('manuscripts/{id}/withdraw', [ManuscriptController::class, 'withdraw']);
    Route::post('manuscripts/{id}/resubmit', [ManuscriptController::class, 'resubmit']);
    Route::post('manuscripts/{id}/archive', [ManuscriptController::class, 'archive']);
    Route::post('manuscripts/{id}/restore', [ManuscriptController::class, 'restore']);
    Route::get('co-author-invitations', [CoAuthorInvitationController::class, 'index']);
    Route::post('co-author-invitations/{id}/respond', [CoAuthorInvitationController::class, 'respond']);

    Route::get('review-invitations', [ReviewInvitationController::class, 'index']);
    Route::get('review-invitations/{id}', [ReviewInvitationController::class, 'show']);
    Route::post('manuscripts/{manuscriptId}/review-invitations', [ReviewInvitationController::class, 'store']);
    Route::post('review-invitations/{id}/respond', [ReviewInvitationController::class, 'respond']);
    Route::post('review-invitations/{id}/request-extension', [ReviewInvitationController::class, 'requestExtension']);
    Route::post('review-invitations/{id}/decide-extension', [ReviewInvitationController::class, 'decideExtension']);
    Route::post('review-invitations/{invitationId}/reviews', [ReviewController::class, 'store']);
    Route::get('reviews/{id}', [ReviewController::class, 'show']);
    Route::get('reviews/{reviewId}/files/{fileId}/download', [ReviewController::class, 'downloadFile']);

    Route::post('manuscripts/{manuscriptId}/editor-assignments', [EditorAssignmentController::class, 'store']);
    Route::post('manuscripts/{manuscriptId}/decisions', [EditorialDecisionController::class, 'store']);
    Route::get('manuscripts/{manuscriptId}/messages', [MessageController::class, 'index']);
    Route::post('manuscripts/{manuscriptId}/messages', [MessageController::class, 'store']);
    Route::post('messages/{id}/read', [MessageController::class, 'markRead']);

    Route::get('issues', [IssueController::class, 'index']);
    Route::post('issues', [IssueController::class, 'store']);
    Route::get('issues/{id}', [IssueController::class, 'show']);
    Route::post('issues/{issueId}/articles', [IssueController::class, 'addArticle']);
    Route::post('issues/{id}/publish', [IssueController::class, 'publish']);

    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);
    Route::patch('articles/{id}', [ArticleController::class, 'update']);
    Route::delete('articles/{id}', [ArticleController::class, 'destroy']);
    Route::get('articles/{id}/download', [ArticleController::class, 'download']);
    Route::post('articles/{id}/metrics/track', [ArticleController::class, 'trackMetric']);
    Route::get('articles/{id}/metrics/today', [ArticleController::class, 'todayMetrics']);

    Route::get('reports/journal-performance', [ReportController::class, 'journalPerformance']);
    Route::get('reports/reviewer-performance', [ReportController::class, 'reviewerPerformance']);

    Route::get('audit-log', [AuditLogController::class, 'index']);
});
