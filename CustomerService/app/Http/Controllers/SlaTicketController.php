<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\SlaCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SlaTicketController extends Controller
{
    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'customer_name' => 'required|string',
            'issue_description' => 'required|string',
            'priority_level' => 'required|in:Critical,High,Medium,Low,critical,high,medium,low',
        ]);

        $sender = $request->input('sender_type', 'Customer');

        // Get current user ID or create a guest user
        $userId = Auth::id() ?? 1;

        $ticket = Ticket::create([
            'ticket_reference' => 'TKT-' . time(),
            'user_id' => $userId,
            'customer_name' => $validated['customer_name'],
            'issue_description' => $validated['issue_description'],
            'priority_level' => ucfirst(strtolower($validated['priority_level'])),
            'priority' => strtolower($validated['priority_level']),
            'subject' => $validated['issue_description'],
            'description' => $validated['issue_description'],
            'category' => 'General Support',
            'status' => 'open',
        ]);

        DB::table('customer_conversations')->insert([
            'ticket_id'          => $ticket->id,
            'sender'             => $sender,
            'communication_type' => 'Chat',
            'message'            => $request->input('message'),
            'sent_at'            => now(),
            'created_at'         => now(),
        ]);

        SlaCalculator::updateSlaData();

        return redirect()->route('sla-reports.index')
            ->with('success', 'Ticket created successfully!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|in:Open,In Progress,Resolved,Closed,open,in-progress,resolved,closed']);
        
        $status = strtolower($request->status);
        
        if (in_array($status, ['in progress', 'in-progress'])) {
            $ticket->update([
                'responded_at' => now(), 
                'status' => 'in-progress'
            ]);
        } elseif ($status === 'resolved') {
            $ticket->update([
                'resolved_at' => now(), 
                'status' => 'resolved'
            ]);
        } else {
            $ticket->update(['status' => $status]);
        }

        SlaCalculator::updateSlaData();

        return redirect()->back()->with('success', 'Ticket updated!');
    }

    public function index()
    {
        $tickets = Ticket::orderBy('created_at', 'desc')->get();
        return view('tickets.index', compact('tickets'));
    }
}