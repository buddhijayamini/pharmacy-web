<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\MedicationController;
use App\Http\Controllers\API\PermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//test
Route::get('/test', function () {
    return "hi";
});

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/users', [AuthController::class, 'index'])->name('users');

    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::get('/{id}', [PermissionController::class, 'show'])->name('show');
        Route::put('/{id}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('destroy');
        Route::post('/roles/assign-permissions', [PermissionController::class, 'assignPermissionsToRole'])->name('assignPermissionsToRole');
    });

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');

        // Owner routes
        Route::middleware('owner')->group(function () {
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
            Route::delete('/{customer}/force', [CustomerController::class, 'forceDelete'])->name('forceDelete')->middleware('confirm_deletion');
        });

        // Manager routes
        Route::middleware('manager')->group(function () {
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        });

        // Cashier routes
        Route::middleware('cashier')->group(function () {
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        });
    });

    Route::prefix('medications')->name('medications.')->group(function () {
        Route::get('/', [MedicationController::class, 'index'])->name('index');

        // Owner routes
        Route::middleware('owner')->group(function () {
            Route::post('/', [MedicationController::class, 'store'])->name('store');
            Route::put('/{medication}', [MedicationController::class, 'update'])->name('update');
            Route::delete('/{medication}', [MedicationController::class, 'destroy'])->name('destroy');
            Route::delete('/{medication}/force', [MedicationController::class, 'forceDelete'])->name('forceDelete')->middleware('confirm_deletion');
        });

        // Manager routes
        Route::middleware('manager')->group(function () {
            Route::put('/{medication}', [MedicationController::class, 'update'])->name('update');
            Route::delete('/{medication}', [MedicationController::class, 'destroy'])->name('destroy');
        });

        // Cashier routes
        Route::middleware('cashier')->group(function () {
            Route::put('/{medication}', [MedicationController::class, 'update'])->name('update');
        });
    });
});
