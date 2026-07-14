<?php

use App\Http\Controllers\Admin\JournalController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Backend routes — called only by Frontend, never the browser directly.
|--------------------------------------------------------------------------
*/

// Public (no auth): login, register. Light throttle just to avoid an
// accidental infinite retry loop — this is a class project, not a
// public-facing service, so it's kept generous on purpose.
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:30,1');
Route::post('register', [AuthController::class, 'register'])->middleware('throttle:30,1');

// Authenticated.
Route::middleware('auth:remote-sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('password/change', [AuthController::class, 'changePassword']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::patch('profile', [AuthController::class, 'profile']);

    // Journal names/scopes aren't sensitive — any authenticated user can
    // list/view them (e.g. Author\CreateSubmission's journal picker).
    // Mutating journal actions stay Admin-only, see modules/admin.php.
    Route::get('journals', [JournalController::class, 'index']);
    Route::get('journals/{id}', [JournalController::class, 'show']);

    require __DIR__.'/modules/manuscripts.php';
    require __DIR__.'/modules/reviews.php';
    require __DIR__.'/modules/editorial.php';
    require __DIR__.'/modules/publication.php';
    require __DIR__.'/modules/analytics.php';
    require __DIR__.'/modules/admin.php';
});

// Public reader access (module 6) — browsing/search doesn't require login;
// download does (enforced inside ReaderController).
require __DIR__.'/modules/reader.php';
