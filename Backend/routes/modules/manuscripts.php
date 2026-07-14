<?php

use App\Http\Controllers\Manuscripts\CoAuthorInvitationController;
use App\Http\Controllers\Manuscripts\CoAuthorSearchController;
use App\Http\Controllers\Manuscripts\ManuscriptController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module 2 — Manuscript submission & tracking.
|--------------------------------------------------------------------------
*/

Route::get('co-authors/search', [CoAuthorSearchController::class, 'index']);

Route::get('manuscripts', [ManuscriptController::class, 'index']);
Route::post('manuscripts', [ManuscriptController::class, 'store']);
Route::get('manuscripts/{id}', [ManuscriptController::class, 'show']);
Route::post('manuscripts/{id}/versions', [ManuscriptController::class, 'storeVersion']);
Route::get('manuscripts/{id}/files/{fileId}/download', [ManuscriptController::class, 'downloadFile']);
Route::post('manuscripts/{id}/versions/{versionId}/set-main', [ManuscriptController::class, 'setMainVersion']);
Route::post('manuscripts/{id}/co-authors/invite', [ManuscriptController::class, 'inviteCoAuthor']);
Route::post('manuscripts/{id}/submit', [ManuscriptController::class, 'submit']);
Route::post('manuscripts/{id}/withdraw', [ManuscriptController::class, 'withdraw']);
Route::post('manuscripts/{id}/resubmit', [ManuscriptController::class, 'resubmit']);
Route::delete('manuscripts/{id}', [ManuscriptController::class, 'destroy']);

Route::get('co-author-invitations', [CoAuthorInvitationController::class, 'index']);
Route::post('co-author-invitations/{id}/respond', [CoAuthorInvitationController::class, 'respond']);
