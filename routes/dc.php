<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\DC\AssignTagOfficerController;
use App\Http\Controllers\Backend\DC\DashboardController;
use App\Http\Controllers\Backend\DC\FillingStationController;
use App\Http\Controllers\Backend\DC\ProfileController;
use App\Http\Controllers\Backend\DC\TagOfficerController;
use App\Http\Controllers\Backend\DC\UnoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DC\DcReportsController;

Route::prefix('dc')->middleware(['role:' . UserRole::DC])->as('dc.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::delete('dashboard/{id}/report-destroy', [DashboardController::class, 'reportDestroy'])->name('dashboard.report.destroy');
    Route::resource('profile', ProfileController::class);
    Route::resource('filling-station', FillingStationController::class);
    Route::resource('uno', UnoController::class);
    Route::resource('tag-officer', TagOfficerController::class);
    Route::resource('assign-tag-officer', AssignTagOfficerController::class);
    // DC routes group
    Route::get('reports/sales', [DcReportsController::class, 'index'])->name('reports.index');
    Route::delete('reports/{id}', [DcReportsController::class, 'destroy'])->name('reports.destroy');
    Route::post('reports/message', [DcReportsController::class, 'sendMessage'])->name('reports.message');

    // reports

    Route::get('reports/sales', [DcReportsController::class, 'index'])->name('reports.index');
    Route::delete('reports/{id}', [DcReportsController::class, 'destroy'])->name('reports.destroy');
    Route::post('reports/message', [DcReportsController::class, 'sendMessage'])->name('reports.message');

    Route::get('dc/reports/difference', [DcReportsController::class, 'differenceReport'])
        ->name('reports.difference');

    Route::get('dc/reports/missing',   [DcReportsController::class, 'missingReport'])
        ->name('reports.missing');

    Route::get('dc/reports/submitted', [DcReportsController::class, 'submittedReport'])
        ->name('reports.submitted');

    // edit reports
    Route::get('reports/{fuelReport}/edit', [DcReportsController::class, 'edit'])->name('reports.edit');
    Route::put('reports/{fuelReport}/update', [DcReportsController::class, 'update'])->name('reports.update');    

    // export pdf routes
    Route::get('reports/export-pdf', [DcReportsController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('reports/export-difference-pdf', [DcReportsController::class, 'exportDifferencePdf'])->name('reports.export.difference.pdf');
    Route::get('reports/export-missing-pdf',    [DcReportsController::class, 'exportMissingPdf'])->name('reports.export.missing.pdf');
    Route::get('reports/export-submitted-pdf',  [DcReportsController::class, 'exportSubmittedPdf'])->name('reports.export.submitted.pdf');
});
