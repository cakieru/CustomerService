<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlaReportController;

Route::get('/sla-reports', [SlaReportController::class, 'index'])->name('sla-reports.index');
Route::get('/sla-reports/export', [SlaReportController::class, 'export'])->name('sla-reports.export');

use App\Http\Controllers\SlaAdminController;

Route::get('/admin/sla', [SlaAdminController::class, 'index'])->name('admin.sla');
Route::put('/admin/sla/update', [SlaAdminController::class, 'update'])->name('admin.sla.update');

use App\Http\Controllers\SlaTicketController;

Route::get('/tickets', [SlaTicketController::class, 'index']);
Route::get('/tickets/create', [SlaTicketController::class, 'create']);
Route::post('/tickets', [SlaTicketController::class, 'store']);
Route::patch('/tickets/{ticket}/status', [SlaTicketController::class, 'updateStatus']);
