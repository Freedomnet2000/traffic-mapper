<?php

use Inertia\Inertia;
use App\Models\Mapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminStatsController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Public Routes (no auth required)
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', fn () => redirect()->route('dashboard'))->name('home');


// Authentication for guests
Route::middleware('guest')->group(function () {
    // show login form
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // show registration form
    Route::get('/register', fn () => Inertia::render('Auth/Register'))
        ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// traffic mapping redirect
Route::get('/redirect', [RedirectController::class, 'handle'])
    ->name('redirect');

// mock affiliate endpoint
Route::get('/mock-affiliate', fn (Request $req) => response()->json([
    'received_param' => $req->query('our_param'),
    'message'        => 'Affiliate mock OK',
]))->name('mock-affiliate');

/*
|--------------------------------------------------------------------------
| Protected Routes (authenticated + verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // user dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/stats', [AdminStatsController::class, 'index'])->name('admin.stats');
    Route::get('/admin/failures', [AdminStatsController::class, 'failures'])->name('admin.failures');


    // profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (authenticated + can:admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // admin dashboard
            Route::get('/', [AdminController::class, 'index'])->name('dashboard');
            // manage users
            Route::get('/users', [AdminController::class, 'users'])->name('users');
        });
});
