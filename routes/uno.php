<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\TagOfficer\DashboardController;
use App\Http\Controllers\Backend\TagOfficer\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('uno')->middleware(['role:'.UserRole::UNO])->as('uno.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::resource('profile', ProfileController::class);
});
