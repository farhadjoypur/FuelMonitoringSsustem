<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\UNO\AssignTagOfficerController;
use App\Http\Controllers\Backend\UNO\DashboardController;
use App\Http\Controllers\Backend\UNO\FillingStationController;
use App\Http\Controllers\Backend\UNO\ProfileController;
use App\Http\Controllers\Backend\UNO\TagOfficerController;
use Illuminate\Support\Facades\Route;

Route::prefix('uno')->middleware(['role:'.UserRole::UNO])->as('uno.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::resource('profile', ProfileController::class);
    Route::resource('filling-station', FillingStationController::class);
    Route::resource('tag-officer', TagOfficerController::class);
    Route::resource('assign-tag-officer', AssignTagOfficerController::class);
});
