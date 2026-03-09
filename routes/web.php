<?php

use App\Http\Controllers\Web\ApplicationTrackingController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\LogoutController;
use App\Http\Controllers\Web\RecruitmentController;
use Illuminate\Support\Facades\Route;

/* ----------------------------------------------------------------
 |  Public zone
 | ---------------------------------------------------------------- */

Route::get('/', HomeController::class)->name('home');
Route::get('/recruitment', RecruitmentController::class)->name('recruitment');
Route::get('/recruitment/tracking', ApplicationTrackingController::class)->name('recruitment.tracking');
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/logout', LogoutController::class)->name('logout');

/* ----------------------------------------------------------------
 |  Private zone — Authenticated agents
 | ---------------------------------------------------------------- */

Route::middleware(['auth', 'agent.active'])->group(function () {

    Route::get('/dashboard', App\Http\Controllers\Agent\DashboardController::class)->name('dashboard');

    // Reports (all users — access controlled per report)
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [App\Http\Controllers\ReportController::class, 'create'])->name('reports.create');
    Route::get('/reports/{report}', [App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/edit', [App\Http\Controllers\ReportController::class, 'edit'])->name('reports.edit');

    // Library (all users — access controlled per document)
    Route::get('/library', [App\Http\Controllers\LibraryController::class, 'index'])->name('library.index');
    Route::get('/library/create', [App\Http\Controllers\LibraryController::class, 'create'])->name('library.create');
    Route::get('/library/{document}', [App\Http\Controllers\LibraryController::class, 'show'])->name('library.show');
    Route::get('/library/{document}/edit', [App\Http\Controllers\LibraryController::class, 'edit'])->name('library.edit');
    Route::get('/library/{document}/download', [App\Http\Controllers\LibraryController::class, 'download'])->name('library.download');
    Route::get('/library/{document}/preview', [App\Http\Controllers\LibraryController::class, 'preview'])->name('library.preview');

    // Categories (all users — create/edit restricted to Director G)
    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [App\Http\Controllers\CategoryController::class, 'edit'])->name('categories.edit');

    Route::get('/reminders', [App\Http\Controllers\Agent\ReminderController::class, 'index'])->name('reminders.index');
    Route::get('/reminders/create', [App\Http\Controllers\Agent\ReminderController::class, 'create'])->name('reminders.create');
    Route::get('/activity', App\Http\Controllers\Agent\ActivityController::class)->name('activity.index');
    Route::get('/profile', [App\Http\Controllers\Agent\ProfileController::class, 'index'])->name('profile');

    /* ----------------------------------------------------------------
     |  Admin zone — Director G only (agents, permissions, applications)
     | ---------------------------------------------------------------- */

    Route::middleware('director')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/agents', [App\Http\Controllers\Admin\AgentController::class, 'index'])->name('agents.index');
        Route::get('/agents/create', [App\Http\Controllers\Admin\AgentController::class, 'create'])->name('agents.create');
        Route::get('/agents/{user}', [App\Http\Controllers\Admin\AgentController::class, 'show'])->name('agents.show');
        Route::get('/agents/{user}/edit', [App\Http\Controllers\Admin\AgentController::class, 'edit'])->name('agents.edit');

        Route::get('/permissions', App\Http\Controllers\Admin\PermissionController::class)->name('permissions.index');

        Route::get('/applications', [App\Http\Controllers\Admin\ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [App\Http\Controllers\Admin\ApplicationController::class, 'show'])->name('applications.show');
    });
});
