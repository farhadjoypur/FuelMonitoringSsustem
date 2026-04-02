<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['role:'.UserRole::ADMIN])->as('admin.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
});
