<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\UNO\DashboardController;
use App\Http\Controllers\Backend\UNO\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('uno')->middleware(['role:'.UserRole::UNO])->as('uno.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::resource('profile', ProfileController::class);
});
