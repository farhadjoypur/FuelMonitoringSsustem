<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Admin\CompanyController;
use App\Http\Controllers\Backend\Admin\FillingStationController;

Route::prefix('admin')->middleware(['role:'.UserRole::ADMIN])->as('admin.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
});

Route::prefix('admin')->group(function () {
    Route::resource('companies', CompanyController::class)->names('companies');
});

Route::prefix('admin')->group(function () {
    Route::resource('stations', FillingStationController::class)->names('stations');
    Route::get('/stations/{station}/get', [FillingStationController::class, 'getStation']);
});