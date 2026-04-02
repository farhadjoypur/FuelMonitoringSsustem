<?php

use App\Http\Controllers\Backend\DC\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dc')->as('dc.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
});
