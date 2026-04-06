<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\Admin\DcReportsController;
use App\Http\Controllers\Backend\DC\AssignTagOfficerController;
use App\Http\Controllers\Backend\DC\DashboardController;
use App\Http\Controllers\Backend\DC\FillingStationController;
use App\Http\Controllers\Backend\DC\ProfileController;
use App\Http\Controllers\Backend\DC\TagOfficerController;
use Illuminate\Support\Facades\Route;

Route::prefix('dc')->middleware(['role:'.UserRole::DC])->as('dc.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::resource('profile', ProfileController::class);
    Route::resource('stations', FillingStationController::class)->names('stations');
    Route::get('/stations/{station}/get', [FillingStationController::class, 'getStation']);
    Route::resource('tag-officer', TagOfficerController::class);
    Route::resource('assign-tag-officer', AssignTagOfficerController::class);
    // DC routes group
    Route::get('/reports', [DcReportsController::class, 'index'])->name('reports.index');
});
