<?php

use App\Http\Controllers\Backend\TagOfficer\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('tag-officer')->as('tag-officer.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
});
