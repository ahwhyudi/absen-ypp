<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\LeaveRequestController;

/*
|--------------------------------------------------------------------------
| Homepage
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    if (auth()->check()) {

        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            return redirect()->route('dashboard.admin');
        }

        if ($user->hasRole('Manager')) {
            return redirect()->route('dashboard.manager');
        }

        if ($user->hasRole('Staff')) {
            return redirect()->route('dashboard.staff');
        }
    }

    return redirect()->route('login');

});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'index'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'authenticate'])
        ->name('login.authenticate');

});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

        });

    /*
    |--------------------------------------------------------------------------
    | Manager
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:manager')
        ->prefix('manager')
        ->name('manager.')
        ->group(function () {

            Route::get('/dashboard', [ManagerDashboardController::class, 'index'])
                ->name('dashboard');

        });

    /*
    |--------------------------------------------------------------------------
    | Staff
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:employee')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */

            Route::prefix('attendance')
                ->name('attendance.')
                ->group(function () {

                    Route::get('/', [StaffDashboardController::class, 'index'])
                        ->name('index');

                    Route::post('/check', [StaffDashboardController::class, 'store'])
                        ->name('store');

                    Route::get('/history', [StaffDashboardController::class, 'history'])
                        ->name('history');

                });

            /*
            |--------------------------------------------------------------------------
            | Leave Request
            |--------------------------------------------------------------------------
            */

            // Route::prefix('leave-request')
            //     ->name('leave-request.')
            //     ->group(function () {

            //         Route::get('/', [LeaveRequestController::class, 'index'])
            //             ->name('index');

            //         Route::get('/create', [LeaveRequestController::class, 'create'])
            //             ->name('create');

            //         Route::post('/', [LeaveRequestController::class, 'store'])
            //             ->name('store');

            //     });

        });

});