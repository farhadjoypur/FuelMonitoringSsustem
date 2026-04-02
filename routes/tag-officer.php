<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\TagOfficer\DashboardController;
use App\Http\Controllers\Backend\TagOfficer\SalesReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('tag-officer')->middleware(['role:'.UserRole::TAG_OFFICER])->as('tag-officer.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::resource('sales-report', SalesReportController::class);
});
