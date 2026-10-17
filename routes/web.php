<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ReviewModerationController;
use App\Http\Controllers\Admin\SalonModerationController;
use App\Http\Controllers\Admin\UserModerationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SalonController;
use App\Http\Controllers\SalonOpeningHourController;
use App\Http\Controllers\SalonRegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('salons.index'))
    ->name('home');

Route::get('/salons', [SalonController::class, 'index'])
    ->name('salons.index');

Route::get('/salons/{salon}', [SalonController::class, 'show'])
    ->name('salons.show');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated and active users
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->group(function () {

    /*
    | Submit a salon listing.
    | Customers become owners after a successful submission.
    */

    Route::get('/list-your-salon', [SalonRegistrationController::class, 'create'])
        ->name('salons.owner-register');

    Route::post('/list-your-salon', [SalonRegistrationController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('salons.owner-register.store');

    /*
    | Reviews
    */

    Route::post('/salons/{salon}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');

    Route::put('/reviews/{review}', [ReviewController::class, 'update'])
        ->name('reviews.update');

    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy');

    /*
    |--------------------------------------------------------------------------
    | Owner dashboard
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:owner')
        ->prefix('owner')
        ->name('owner.')
        ->group(function () {
            Route::get('/dashboard', [SalonController::class, 'dashboard'])
                ->name('dashboard');

            Route::get('/salon/create', [SalonController::class, 'create'])
                ->name('salon.create');

            Route::post('/salon', [SalonController::class, 'store'])
                ->name('salon.store');

            Route::get('/salon/edit', [SalonController::class, 'edit'])
                ->name('salon.edit');

            Route::put('/salon', [SalonController::class, 'update'])
                ->name('salon.update');

            Route::delete('/salon', [SalonController::class, 'destroy'])
                ->name('salon.destroy');

            Route::put(
                '/salon/opening-hours',
                [SalonOpeningHourController::class, 'update']
            )->name('salon.opening-hours.update');
        });

    /*
    |--------------------------------------------------------------------------
    | Admin dashboard
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::post(
                '/salons/{salon}/approve',
                [SalonModerationController::class, 'approve']
            )->name('salons.approve');

            Route::post(
                '/salons/{salon}/reject',
                [SalonModerationController::class, 'reject']
            )->name('salons.reject');

            Route::post(
                '/salons/{salon}/suspend',
                [SalonModerationController::class, 'suspend']
            )->name('salons.suspend');

            Route::post(
                '/salons/{salon}/restore',
                [SalonModerationController::class, 'restore']
            )->name('salons.restore');

            Route::post(
                '/users/{user}/suspend',
                [UserModerationController::class, 'suspend']
            )->name('users.suspend');

            Route::post(
                '/users/{user}/restore',
                [UserModerationController::class, 'restore']
            )->name('users.restore');

            Route::post(
                '/reviews/{review}/hide',
                [ReviewModerationController::class, 'hide']
            )->name('reviews.hide');

            Route::post(
                '/reviews/{review}/restore',
                [ReviewModerationController::class, 'restore']
            )->name('reviews.restore');

            Route::delete(
                '/reviews/{review}',
                [ReviewModerationController::class, 'destroy']
            )->name('reviews.destroy');
        });
});