<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlaReportController;
use App\Http\Controllers\SlaAdminController;
use App\Http\Controllers\SlaTicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\KnowledgeBaseController;

/*
|--------------------------------------------------------------------------
| SLA Routes
|--------------------------------------------------------------------------
*/
Route::get('/sla-reports', [SlaReportController::class, 'index'])->name('sla-reports.index');
Route::get('/sla-reports/export', [SlaReportController::class, 'export'])->name('sla-reports.export');

Route::get('/admin/sla', [SlaAdminController::class, 'index'])->name('admin.sla');
Route::put('/admin/sla/update', [SlaAdminController::class, 'update'])->name('admin.sla.update');

Route::prefix('tickets')->group(function () {
    Route::get('/', [SlaTicketController::class, 'index'])->name('tickets.index');
    Route::get('/create', [SlaTicketController::class, 'create'])->name('tickets.create');
    Route::post('/', [SlaTicketController::class, 'store'])->name('tickets.store');
    Route::patch('/{ticket}/status', [SlaTicketController::class, 'updateStatus'])->name('tickets.updateStatus');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.support.dashboard');
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('admin.support.tickets.index');
    Route::get('/tickets/{ticket}', [AdminController::class, 'show'])->name('admin.support.tickets.show');
    Route::post('/tickets/{ticket}/status', [AdminController::class, 'updateStatus'])->name('admin.tickets.status');
    Route::post('/tickets/{ticket}/assign', [AdminController::class, 'assignAgent'])->name('admin.tickets.assign');
    Route::post('/tickets/{ticket}/reply', [AdminController::class, 'reply'])->name('admin.tickets.reply');

    // Admin reports now redirects to full SLA reports page
    Route::get('/reports', function () {
        return redirect()->route('sla-reports.index');
    })->name('admin.support.reports');
});

/*
|--------------------------------------------------------------------------
| Customer Portal Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [CustomerController::class, 'home'])->name('CustomerPortal');
Route::get('/customer', [CustomerController::class, 'home'])->name('customer');
Route::get('/customer/home', [CustomerController::class, 'home'])->name('customer.home');

Route::prefix('customer')->group(function () {
    Route::get('/tickets', [CustomerController::class, 'index'])->name('customer.tickets');
    Route::get('/tickets/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/tickets', [CustomerController::class, 'store'])->name('customer.tickets.store');
    Route::get('/tickets/{ticket}', [CustomerController::class, 'show'])->name('customer.tickets.show');
    Route::post('/tickets/{ticket}/reply', [CustomerController::class, 'reply'])->name('customer.tickets.reply');
});


/*
|--------------------------------------------------------------------------
| Legacy Ticket Routes (for customer conversation system)
|--------------------------------------------------------------------------
*/
Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
Route::post('/tickets/{ticket_id}/reply', [TicketController::class, 'storeReply'])->name('tickets.reply');

/*
|--------------------------------------------------------------------------
| Knowledge Base Routes
|--------------------------------------------------------------------------
*/
Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('KnowledgeBase');
Route::get('/knowledge-base/customer', [KnowledgeBaseController::class, 'customerIndex'])->name('kb.customer');
Route::post('/knowledge-base', [KnowledgeBaseController::class, 'store'])->name('kb.store');
Route::put('/knowledge-base/{id}', [KnowledgeBaseController::class, 'update'])->name('kb.update');
Route::post('/knowledge-base/{id}/vote', [KnowledgeBaseController::class, 'vote'])->name('kb.vote');
Route::post('/knowledge-base/{id}/view', [KnowledgeBaseController::class, 'incrementView'])->name('kb.view');

/*
|--------------------------------------------------------------------------
| Agent Routes
|--------------------------------------------------------------------------
*/
Route::get('/agent', function () {
    return view('agent');
})->name('agent');

// Agent Portal - Dashboard List View
Route::get('/agent', [AdminController::class, 'agentTickets'])->name('agent');

Route::get('/agent/ticket/details', function () {
    return view('details');
})->name('agent.ticket.details');

/*
|--------------------------------------------------------------------------
| Terms Route
|--------------------------------------------------------------------------
*/
Route::get('/terms', function () {
    return view('terms');
})->name('terms');