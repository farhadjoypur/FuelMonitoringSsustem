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
    Route::get('dc/reports/difference/export-pdf', [DcReportsController::class, 'exportDifferenceReportPdf'])
        ->name('reports.difference.export-pdf');

    Route::get('dc/reports/missing',   [DcReportsController::class, 'missingReport'])
        ->name('reports.missing');

    Route::get('dc/reports/submitted', [DcReportsController::class, 'submittedReport'])
        ->name('reports.submitted');

    //  . reports . missing . export - pdf
    Route::get('dc/reports/missing/export-pdf', [DcReportsController::class, 'exportMissingReportPdf'])
        ->name('reports.missing.export-pdf');
    //  . reports . submitted . export - pdf
    Route::get('dc/reports/submitted/export-pdf', [DcReportsController::class, 'exportSubmittedReportPdf'])
        ->name('reports.submitted.export-pdf');
});
