<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Ticket;


// -----------------------------------------------------
// 1. FALLBACK LOGIN (For testing the Admin Sub-module)
// -----------------------------------------------------
Route::get('/login', function () {
    $admin = User::where('role', 'admin')->first();
    if (!$admin) {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@supportdesk.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
    }
    Auth::login($admin);
    return redirect()->route('admin.support.dashboard');
    
})->name('login');


// -----------------------------------------------------
// 2. CUSTOMER FACING ROUTES
// -----------------------------------------------------
Route::get('/home', [CustomerController::class, 'home'])->name('customer.home');
Route::get('/customer/tickets', [CustomerController::class, 'index'])->name('customer.tickets');
Route::get('/tickets/create', [CustomerController::class, 'create'])->name('customer.create');
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::post('/tickets', [CustomerController::class, 'store'])->name('tickets.store');
Route::get('/tickets/{id}', [CustomerController::class, 'show'])->name('customer.show');
Route::post('/tickets/{ticket}/reply', [CustomerController::class, 'reply'])->name('customer.reply');


// -----------------------------------------------------
// 3. LIVE CHAT API ENDPOINTS
// -----------------------------------------------------
Route::post('/live-chat/start', [CustomerController::class, 'startLiveChat'])->name('live-chat.start');
Route::post('/live-chat/send', [CustomerController::class, 'sendLiveChatMessage'])->name('live-chat.send');
Route::get('/live-chat/messages/{ticket}', function(Ticket $ticket) {
    return response()->json($ticket->replies()->with('user')->get());
});


// -----------------------------------------------------
// 4. ADMIN SUB-MODULE ROUTES
// -----------------------------------------------------
Route::prefix('admin/support')->name('admin.support.')->middleware(['auth'])->group(function () {
    
    // Core Overview
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

    // Advanced Ticket Table & Interaction
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminController::class, 'show'])->name('tickets.show');
    
    // Core Sub-module Actions
    Route::patch('/tickets/{ticket}/status', [AdminController::class, 'updateStatus'])->name('tickets.update-status');
    Route::post('/tickets/{ticket}/assign', [AdminController::class, 'assignAgent'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/reply', [AdminController::class, 'reply'])->name('tickets.reply');
});

//yen yen

// Home / Landing Redirection (Maps to Customer Portal)
Route::get('/', function () {
    return redirect()->route('CustomerPortal');
})->name('home');

// Agent Portal - Dashboard List View
Route::get('/agent', function () {
    return view('admin.agent'); // This renders agent.blade.php (the layout with the data table)
})->name('agent');

// Agent Portal - Single Ticket Detail View
Route::get('/agent/ticket-details', function () {
    return view('details'); // This renders details.blade.php (the agent side detail layout)
})->name('agent.ticket.details');

// Fallback / Alias (Redirects to the main portal)
Route::get('/portal', function () {
    return redirect()->route('CustomerPortal');
})->name('customer');

// Customer Portal - My Tickets List View
Route::get('/tickets.index', function () {
    return view('tickets'); // Renders tickets.blade.php
})->name('tickets');

// Customer Portal - Individual Ticket Details View
Route::get('/ticket-details', function () {
    return view('customerTicket'); // Renders customerTicket.blade.php
})->name('ticket.details');


// Terms & Conditions Static Informational Page 
Route::get('/terms', function () {
    return view('terms'); // Renders terms.blade.php
})->name('terms');


Route::get('/test-db', function () {
    try {
        $tickets = DB::table('support_tickets')->get();
        return response()->json($tickets);
    } catch (\Exception $e) {
        return "Connection failed: " . $e->getMessage();
    }
});



//geong
// UPDATED: Now fetches only the articles dynamically for the Customer Portal view
Route::get('/CustomerPortal', function () {
    // 1. Fetch from database - customers should only ever see publicly-visible articles
    $dbArticles = DB::table('kb_articles')
        ->where('visibility', 'public')
        ->get();

    // 2. Format the columns to match your JavaScript properties
    $articles = $dbArticles->map(function($article) {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'desc' => $article->desc,
            'category' => $article->category,
            'catId' => $article->cat_id,
            'views' => number_format($article->views),
            'updated' => \Carbon\Carbon::parse($article->updated_at)->format('m/d/Y'),
            'helpful' => $article->yes_votes + $article->no_votes > 0 
                ? round(($article->yes_votes / ($article->yes_votes + $article->no_votes)) * 100) . '%' 
                : '100%',
            'tags' => explode(',', $article->tags),
            'yesVotes' => $article->yes_votes,
            'noVotes' => $article->no_votes,
        ];
    });

    // 4. Pass it to the view
    return view('CustomerPortal', compact('articles'));
})->name('CustomerPortal');

// Knowledge Base Routes (Explicit strings to prevent editor confusion)
Route::get('/KnowledgeBase', ['App\Http\Controllers\KnowledgeBaseController', 'index'])->name('KnowledgeBase');
Route::get('/SelfServicePortal', ['App\Http\Controllers\KnowledgeBaseController', 'index']);
Route::get('/knowledge-base', ['App\Http\Controllers\KnowledgeBaseController', 'index'])->name('kb.index');
Route::post('/knowledge-base/store', ['App\Http\Controllers\KnowledgeBaseController', 'store'])->name('kb.store');
Route::put('/knowledge-base/{id}', ['App\Http\Controllers\KnowledgeBaseController', 'update'])->name('kb.update');
Route::post('/knowledge-base/{id}/vote', ['App\Http\Controllers\KnowledgeBaseController', 'vote'])->name('kb.vote');
Route::post('/knowledge-base/{id}/view', ['App\Http\Controllers\KnowledgeBaseController', 'incrementView'])->name('kb.view');