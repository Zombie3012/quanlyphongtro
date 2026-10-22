<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;

Route::get('/', [AuthController::class, 'showLogin'])
    ->name('home');

// Login
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');


// Các trang yêu cầu đăng nhập
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'admin'])
            ->name('admin.dashboard');

        Route::resource('properties', PropertyController::class);

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])
                ->name('index');

            Route::get('/create', [UserController::class, 'create'])
                ->name('create');

            Route::post('/', [UserController::class, 'store'])
                ->name('store');

            Route::get('/{user}/edit', [UserController::class, 'edit'])
                ->name('edit');

            Route::put('/{user}', [UserController::class, 'update'])
                ->name('update');

            Route::delete('/{user}', [UserController::class, 'destroy'])
                ->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Quản lý theo từng khu nhà trọ
    |--------------------------------------------------------------------------
    */

    Route::prefix('properties/{property}')
        ->middleware('property.access')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'property'])
                ->name('properties.dashboard');

            /*
            |--------------------------------------------------------------------------
            | Rooms
            |--------------------------------------------------------------------------
            | Không có trang Show theo thiết kế hiện tại.
            */

            Route::resource('rooms', RoomController::class)
                ->except(['show']);

            Route::resource('tenants', TenantController::class);


            Route::resource('contracts', ContractController::class);

            Route::resource('invoices', InvoiceController::class);
        });

});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
