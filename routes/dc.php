<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\DC\AssignTagOfficerController;
use App\Http\Controllers\Backend\DC\DashboardController;
use App\Http\Controllers\Backend\DC\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('dc')->middleware(['role:'.UserRole::DC])->as('dc.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::resource('profile', ProfileController::class);
    Route::resource('assign-tag-officer', AssignTagOfficerController::class);

});
