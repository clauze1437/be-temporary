<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\DriverController;
use App\Http\Controllers\Web\VehicleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/signin', [AuthController::class, 'signIn']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // route role superadmin & admin
    Route::middleware('role:superadmin,admin')->group(function() {
        // route info login
        Route::get('info/admin', [UserController::class, 'getAdminInfo']);

        // route vehicles
        Route::get('vehicles', [VehicleController::class, 'index']);
        Route::post('vehicles', [VehicleController::class, 'store']);
        Route::get('vehicles/{id}', [VehicleController::class, 'show']);

        // route drivers
        Route::get('drivers', [DriverController::class, 'index']);
        Route::post('drivers', [DriverController::class, 'store']);
        Route::get('drivers/{id}', [DriverController::class, 'show']);

        Route::middleware('role:superadmin')->group(function () {
            // route vehicles
            Route::put('vehicles/{id}', [VehicleController::class, 'update']);
            Route::delete('vehicles/{id}', [VehicleController::class, 'destroy']);

            // route drivers
            Route::put('drivers/{id}', [DriverController::class, 'update']);
            Route::delete('drivers/{id}', [DriverController::class, 'destroy']);
        });
    });

    // route role driver
    Route::middleware('role:driver')->group(function () {
        Route::get('info/driver', [UserController::class, 'getDriverInfo']);
    });

    // role yang belum dibuat
    // 1. crud manage admin
    // 2. crud delivery orders
    // 3. ubah password driver dan admin
});
