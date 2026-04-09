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
use App\Http\Controllers\Backend\Admin\ReportsController;
use App\Http\Controllers\Backend\Admin\TagOfficerController;
use App\Http\Controllers\Backend\Admin\UnoController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['role:'.UserRole::ADMIN])->as('admin.')->group(function () {
    Route::resource('companies', CompanyController::class)->names('companies');
    // Route::resource('stations', FillingStationController::class)->names('stations');
    //  Route::get('/stations/{station}/get', [FillingStationController::class, 'getStation']);
    Route::resource('depots', DepotController::class);
    Route::get('depots/{id}/get', [DepotController::class, 'getDepot'])->name('depots.get');

    // KB UTSHO
    Route::resource('dashboard', DashboardController::class);
    Route::resource('profile', ProfileController::class);
    Route::resource('filling-station', FillingStationController::class);
    Route::resource('tag-officer', TagOfficerController::class);
    Route::resource('dc-officer', DcOfficerController::class);
    Route::resource('uno', UnoController::class);
    Route::resource('assign-tag-officer', AssignTagOfficerController::class);
    Route::resource('admin-user', AdminUserController::class);

    // reports

    Route::get('reports/sales', [ReportsController::class, 'index'])->name('reports.index');
    Route::delete('reports/{id}', [ReportsController::class, 'destroy'])->name('reports.destroy');
    Route::post('reports/message', [ReportsController::class, 'sendMessage'])->name('reports.message');

    Route::get('admin/reports/difference', [ReportsController::class, 'differenceReport'])
        ->name('reports.difference');
    Route::get('admin/reports/difference/export-pdf', [ReportsController::class, 'exportDifferenceReportPdf'])
        ->name('reports.difference.export-pdf');

    Route::get('admin/reports/missing',   [ReportsController::class, 'missingReport'])
        ->name('reports.missing');

    Route::get('admin/reports/submitted', [ReportsController::class, 'submittedReport'])
        ->name('reports.submitted');

    // admin . reports . missing . export - pdf
    Route::get('admin/reports/missing/export-pdf', [ReportsController::class, 'exportMissingReportPdf'])
        ->name('reports.missing.export-pdf');
    // admin . reports . submitted . export - pdf
    Route::get('admin/reports/submitted/export-pdf', [ReportsController::class, 'exportSubmittedReportPdf'])
        ->name('reports.submitted.export-pdf');

});
