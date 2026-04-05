<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\TagOfficer\DashboardController;
use App\Http\Controllers\Backend\TagOfficer\SalesReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\TagOfficer\FuelReportController;
use App\Http\Controllers\Backend\TagOfficer\ProfileController;

Route::prefix('tag-officer')->middleware(['role:'.UserRole::TAG_OFFICER])->as('tag-officer.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::resource('profile', ProfileController::class);
    Route::resource('sales-report', SalesReportController::class);
});

Route::prefix('tag-officer')->name('fuel-reports.')->middleware(['auth'])->group(function () {
 
    Route::get('/fuel-reports', [FuelReportController::class, 'index'])->name('index');
    Route::get('/fuel-reports/create', [FuelReportController::class, 'create'])->name('create');
    Route::post('/fuel-reports', [FuelReportController::class, 'store'])->name('store');
    Route::get('/fuel-reports/{fuelReport}', [FuelReportController::class, 'show'])->name('show');
    Route::get('/fuel-reports/{fuelReport}/edit', [FuelReportController::class, 'edit'])->name('edit');
    Route::put('/fuel-reports/{fuelReport}', [FuelReportController::class, 'update'])->name('update');
    Route::delete('/fuel-reports/{fuelReport}', [FuelReportController::class, 'destroy'])->name('destroy');

    Route::get('fuel-reports/export/pdf',   [FuelReportController::class, 'exportPdf'])->name('fuel-reports.export.pdf');
    Route::get('fuel-reports/export/excel', [FuelReportController::class, 'exportExcel'])->name('fuel-reports.export.excel');
 
    // AJAX Route: Previous Stock Auto-fill এর জন্য
    Route::post('/fuel-reports/get-previous-stocks', [FuelReportController::class, 'getPreviousStocks'])->name('previous-stocks');
});