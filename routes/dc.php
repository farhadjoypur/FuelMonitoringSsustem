<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\DC\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dc')->middleware(['role:'.UserRole::DC])->as('dc.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
});
