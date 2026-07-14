<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\JournalController;
use App\Http\Controllers\Admin\ManuscriptController as AdminManuscriptController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserRoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module 1 (admin parts) + Module 5 (journal admin) — Admin only.
|--------------------------------------------------------------------------
*/

Route::middleware('role:Admin')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::patch('users/{id}', [UserController::class, 'update']);
    Route::post('users/{id}/roles', [UserRoleController::class, 'store']);
    Route::delete('users/{id}/roles/{roleId}', [UserRoleController::class, 'destroy']);

    // GET journals (index/show) is registered in routes/api.php for all
    // authenticated users, not just Admin — only mutations stay here.
    Route::patch('journals/{id}', [JournalController::class, 'update']);
    Route::post('journals/{id}/archive', [JournalController::class, 'archive']);
    Route::post('journals/{id}/restore', [JournalController::class, 'restore']);

    Route::get('audit-log', [AuditLogController::class, 'index']);

    // GET manuscripts/{id} is already open to any authenticated user via
    // routes/modules/manuscripts.php — only these admin-only mutations live here.
    Route::post('manuscripts/{id}/return', [AdminManuscriptController::class, 'returnToAuthor']);
    Route::post('manuscripts/{id}/archive', [AdminManuscriptController::class, 'archive']);
    Route::post('manuscripts/{id}/restore', [AdminManuscriptController::class, 'restore']);
});

// Journal creation is open to Author too, not just Admin — authors need to be
// able to add a journal inline while submitting a manuscript. All other
// journal mutations (above) stay Admin-only.
Route::middleware('role:Author,Admin')->post('journals', [JournalController::class, 'store']);
