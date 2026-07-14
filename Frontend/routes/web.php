<?php

use App\Http\Controllers\DashboardHomeController;
use App\Http\Controllers\FileProxyController;
use App\Http\Controllers\LogoutController;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\AuditLog;
use App\Livewire\Admin\JournalManagement;
use App\Livewire\Admin\ManuscriptDetail as AdminManuscriptDetail;
use App\Livewire\Admin\ManuscriptManagement;
use App\Livewire\Admin\RoleManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Analytics\AnalyticsDashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Author\AuthorDashboard;
use App\Livewire\Author\AuthorMetrics;
use App\Livewire\Author\CoAuthoredSubmissions;
use App\Livewire\Author\CoAuthorInvitations;
use App\Livewire\Author\CreateSubmission;
use App\Livewire\Author\MySubmissions;
use App\Livewire\Author\SubmissionDetail;
use App\Livewire\Editor\EditorDashboard;
use App\Livewire\Editor\EditorialDecision;
use App\Livewire\Editor\EditorialDetail;
use App\Livewire\Editor\ReviewerSearch;
use App\Livewire\Editor\ReviewMonitoring;
use App\Livewire\Editor\ViewReviewDetail;
use App\Livewire\Production\CreateIssue;
use App\Livewire\Production\IssueManagement;
use App\Livewire\Production\ManageIssue;
use App\Livewire\Production\ProductionDashboard;
use App\Livewire\Profile;
use App\Livewire\Public\ArticleDetail;
use App\Livewire\Public\BrowseIssues;
use App\Livewire\Public\IssueDetail;
use App\Livewire\Public\PublicHome;
use App\Livewire\Reader\ReaderDashboard;
use App\Livewire\Reviewer\ReviewerDashboard;
use App\Livewire\Reviewer\ReviewHistory;
use App\Livewire\Reviewer\ReviewHistoryDetail;
use App\Livewire\Reviewer\ReviewInvitation;
use App\Livewire\Reviewer\SubmitReview;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', PublicHome::class)->name('public.home');
Route::get('/articles/{articleId}', ArticleDetail::class)->name('public.article-detail');
Route::get('/browse-issues', BrowseIssues::class)->name('public.browse-issues');
Route::get('/issues/{issueId}', IssueDetail::class)->name('public.issue-detail');
Route::view('/access-denied', 'access-denied')->name('access-denied');
Route::get('/files/{path}', FileProxyController::class)->where('path', '.*')->name('files.show');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::post('/logout', LogoutController::class)->name('logout');
});

/*
|--------------------------------------------------------------------------
| Dashboard (auth required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth.session')->prefix('dashboard')->group(function () {
    Route::get('/', DashboardHomeController::class)->name('dashboard.home');
    Route::get('/profile', Profile::class)->name('profile');

    // Author
    Route::middleware('role:Author')->prefix('author')->name('author.')->group(function () {
        Route::get('/', AuthorDashboard::class)->name('dashboard');
        Route::get('/submissions', MySubmissions::class)->name('submissions');
        Route::get('/submissions/create', CreateSubmission::class)->name('submissions.create');
        Route::get('/submissions/{submissionId}', SubmissionDetail::class)->name('submissions.show');
        Route::get('/co-author-invitations', CoAuthorInvitations::class)->name('co-author-invitations');
        Route::get('/co-authored-submissions', CoAuthoredSubmissions::class)->name('co-authored-submissions');
        Route::get('/metrics', AuthorMetrics::class)->name('metrics');
    });

    // Reader
    Route::middleware('role:Reader,Author,Reviewer,Editor,Admin')->prefix('reader')->name('reader.')->group(function () {
        Route::get('/', ReaderDashboard::class)->name('dashboard');
    });

    // Reviewer
    Route::middleware('role:Reviewer')->prefix('reviewer')->name('reviewer.')->group(function () {
        Route::get('/', ReviewerDashboard::class)->name('dashboard');
        Route::get('/invitations/{invitationId}', ReviewInvitation::class)->name('invitations.show');
        Route::get('/reviews/{invitationId}', SubmitReview::class)->name('reviews.submit');
        Route::get('/history', ReviewHistory::class)->name('history');
        Route::get('/history/{reviewId}', ReviewHistoryDetail::class)->name('history.show');
    });

    // Editor
    Route::middleware('role:Editor,Admin')->prefix('editor')->name('editor.')->group(function () {
        Route::get('/', EditorDashboard::class)->name('dashboard');
        Route::get('/submissions/{submissionId}', EditorialDetail::class)->name('submissions.show');
        Route::get('/reviewers/search', ReviewerSearch::class)->name('reviewers.search');
        Route::get('/reviews/{submissionId}', ReviewMonitoring::class)->name('reviews.monitor');
        Route::get('/review-detail/{reviewId}', ViewReviewDetail::class)->name('review-detail');
        Route::get('/decision/{submissionId}', EditorialDecision::class)->name('decision');
    });

    // Production (editor-accessible)
    Route::middleware('role:Editor,Admin')->prefix('production')->name('production.')->group(function () {
        Route::get('/', ProductionDashboard::class)->name('dashboard');
        Route::get('/issues', IssueManagement::class)->name('issues');
        Route::get('/issues/create', CreateIssue::class)->name('issues.create');
        Route::get('/issues/{issueId}', ManageIssue::class)->name('issues.manage');
    });

    // Analytics (editor-accessible)
    Route::middleware('role:Editor,Admin')->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', AnalyticsDashboard::class)->name('dashboard');
    });

    // Admin
    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboard::class)->name('dashboard');
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/roles', RoleManagement::class)->name('roles');
        Route::get('/journals', JournalManagement::class)->name('journals');
        Route::get('/manuscripts', ManuscriptManagement::class)->name('manuscripts');
        Route::get('/manuscripts/{submissionId}', AdminManuscriptDetail::class)->name('manuscripts.show');
        Route::get('/audit', AuditLog::class)->name('audit');
    });
});
