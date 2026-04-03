<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\Admin\AdminUserController;
use App\Http\Controllers\Backend\Admin\AssignTagOfficerController;
use App\Http\Controllers\Backend\Admin\CompanyController;
use App\Http\Controllers\Backend\Admin\DashboardController;
use App\Http\Controllers\Backend\Admin\DcOfficerController;
use App\Http\Controllers\Backend\Admin\DepotController;
use App\Http\Controllers\Backend\Admin\FillingStationController;
use App\Http\Controllers\Backend\Admin\ProfileController;
use App\Http\Controllers\Backend\Admin\TagOfficerController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['role:'.UserRole::ADMIN])->as('admin.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::resource('profile', ProfileController::class);
    Route::resource('companies', CompanyController::class)->names('companies');
    Route::resource('stations', FillingStationController::class)->names('stations');
    Route::get('/stations/{station}/get', [FillingStationController::class, 'getStation']);
    Route::resource('depots', DepotController::class);
    Route::get('depots/{id}/get', [DepotController::class, 'getDepot'])->name('depots.get');
    Route::resource('tag-officer', TagOfficerController::class);
    Route::resource('dc-officer', DcOfficerController::class);
    Route::resource('assign-tag-officer', AssignTagOfficerController::class);
    Route::resource('admin-user', AdminUserController::class);
});
