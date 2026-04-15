<?php

use App\Enums\UserRole;
use App\Http\Controllers\Backend\UNO\AssignTagOfficerController;
use App\Http\Controllers\Backend\UNO\DashboardController;
use App\Http\Controllers\Backend\UNO\FillingStationController;
use App\Http\Controllers\Backend\UNO\ProfileController;
use App\Http\Controllers\Backend\UNO\TagOfficerController;
use App\Http\Controllers\Backend\UNO\UnoReportsController;
use Illuminate\Support\Facades\Route;

Route::prefix('uno')->middleware(['role:' . UserRole::UNO])->as('uno.')->group(function () {
    Route::resource('dashboard', DashboardController::class);
    Route::delete('dashboard/{id}/report-destroy', [DashboardController::class, 'reportDestroy'])->name('dashboard.report.destroy');
    Route::resource('profile', ProfileController::class);
    Route::resource('filling-station', FillingStationController::class);
    Route::resource('tag-officer', TagOfficerController::class);
    Route::resource('assign-tag-officer', AssignTagOfficerController::class);

    // UNO Reports
    Route::get('reports/sales', [UnoReportsController::class, 'index'])->name('reports.index');
    Route::delete('reports/{id}', [UnoReportsController::class, 'destroy'])->name('reports.destroy');
    Route::post('reports/message', [UnoReportsController::class, 'sendMessage'])->name('reports.message');

    Route::get('reports/difference', [UnoReportsController::class, 'differenceReport'])
        ->name('reports.difference');
    Route::get('reports/difference/export-pdf', [UnoReportsController::class, 'exportDifferenceReportPdf'])
        ->name('reports.difference.export-pdf');

    Route::get('reports/missing', [UnoReportsController::class, 'missingReport'])
        ->name('reports.missing');

    Route::get('reports/submitted', [UnoReportsController::class, 'submittedReport'])
        ->name('reports.submitted');

    // export pdf routes
    Route::get('reports/export-pdf', [UnoReportsController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::get('reports/export-difference-pdf', [UnoReportsController::class, 'exportDifferencePdf'])->name('reports.export.difference.pdf');
    Route::get('reports/export-missing-pdf',    [UnoReportsController::class, 'exportMissingPdf'])->name('reports.export.missing.pdf');
    Route::get('reports/export-submitted-pdf',  [UnoReportsController::class, 'exportSubmittedPdf'])->name('reports.export.submitted.pdf');
});
