<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard
     */
    public function index() 
    {
        $summary = [
            'openTickets' => Ticket::where('status', 'open')->count(),
            'inProgress' => Ticket::where('status', 'in-progress')->count(),
            'resolvedToday' => Ticket::where('status', 'resolved')->whereDate('updated_at', today())->count(),
            'criticalPriority' => Ticket::where('priority', 'critical')->where('status', '!=', 'closed')->count()
        ];

        $recentTickets = Ticket::with('customer')->orderBy('created_at', 'desc')->take(5)->get();
        
        $slaAlerts = Ticket::with('agent')
            ->where('status', '!=', 'closed')
            ->where('status', '!=', 'resolved')
            ->where('due_date', '<', now())
            ->get();

        // Fetch the 5 most recent unread/open tickets for the real-time notification drawer
        $notifications = Ticket::with('customer')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ✨ Matches your exact renamed file: resources/views/admin/dashboard.blade.php
        return view('admin.dashboard', compact('summary', 'recentTickets', 'slaAlerts', 'notifications'));
    }

    //jerard baluyot
    
    /**
     * Show a Single Ticket Management Panel
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['customer', 'agent', 'replies.user']);
        $admins = User::where('role', 'admin')->get();

        // Uniform dynamic notification tray data
        $notifications = Ticket::with('customer')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ✨ Matches your file: resources/views/admin/show.blade.php
        return view('admin.show', compact('ticket', 'admins', 'notifications'));
    }

    /**
     * Advanced Ticket Data Matrix Table view
     */
    public function tickets(Request $request)
    {
        $query = Ticket::with(['customer', 'agent']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('ticket_reference', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $notifications = Ticket::with('customer')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.tickets.TicketsInd', compact('tickets', 'notifications'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|in:open,in-progress,resolved,closed']);
        $ticket->update(['status' => $request->status]);
        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function assignAgent(Request $request, Ticket $ticket)
    {
        $request->validate(['agent_id' => 'required|exists:users,id']);
        $ticket->update(['agent_id' => $request->agent_id]);
        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate(['body' => 'required|string']);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id() ?? 1, 
            'body' => $request->body,
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in-progress']);
        }

        return back()->with('success', 'Reply posted successfully.');
    }

    public function reports()
    {
        $totalResolved = Ticket::where('status', 'resolved')->count();
        $notifications = Ticket::with('customer')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return view('admin.reports', compact('totalResolved', 'notifications'));
    }
}