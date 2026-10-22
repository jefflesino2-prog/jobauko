<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\Admin\OpportunityController as AdminOpportunityController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
Route::get('/opportunities/{opportunity}', [OpportunityController::class, 'show'])->name('opportunities.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/opportunities/{opportunity}/apply', [ApplicationController::class, 'store'])->name('apply');
    Route::post('/opportunities/{opportunity}/save', [BookmarkController::class, 'toggle'])->name('save');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/dashboard/notices/read-all', [DashboardController::class, 'markAllRead'])->name('notices.readAll');
    Route::post('/dashboard/notices/{notice}/read', [DashboardController::class, 'markRead'])->name('notices.read');
    Route::post('/dashboard/applications/{application}/advance', [DashboardController::class, 'advance'])->name('applications.advance');
});

// Administrator area: publish and manage programs and opportunities.
Route::middleware(['auth', EnsureAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.opportunities.index'))->name('home');
    Route::get('/opportunities', [AdminOpportunityController::class, 'index'])->name('opportunities.index');
    Route::get('/opportunities/create', [AdminOpportunityController::class, 'create'])->name('opportunities.create');
    Route::post('/opportunities', [AdminOpportunityController::class, 'store'])->name('opportunities.store');
    Route::get('/opportunities/{opportunity}/edit', [AdminOpportunityController::class, 'edit'])->name('opportunities.edit');
    Route::put('/opportunities/{opportunity}', [AdminOpportunityController::class, 'update'])->name('opportunities.update');
    Route::patch('/opportunities/{opportunity}/status', [AdminOpportunityController::class, 'setStatus'])->name('opportunities.status');
});
