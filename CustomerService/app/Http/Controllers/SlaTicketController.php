<?php

namespace App\Http\Controllers;

use App\Models\SlaTicket;  // Changed from Ticket
use App\Services\SlaCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'priority_level' => 'required|in:Critical,High,Medium,Low',
        ]);

        $sender = $request->input('sender_type', 'Customer'); 

        $ticket = SlaTicket::create([  // Changed from Ticket::create
            'ticket_number' => 'TKT-' . time(),
            'customer_name' => $validated['customer_name'],
            'issue_description' => $validated['issue_description'],
            'priority_level' => $validated['priority_level'],
            'status' => 'Open',
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

    public function updateStatus(Request $request, SlaTicket $ticket)  // Changed type-hint
    {
        if ($request->status === 'In Progress') {
            $ticket->update(['responded_at' => now(), 'status' => 'In Progress']);
        }
        
        if ($request->status === 'Resolved') {
            $ticket->update(['resolved_at' => now(), 'status' => 'Resolved']);
        }

        SlaCalculator::updateSlaData();

        return redirect()->back()->with('success', 'Ticket updated!');
    }

    public function index()
    {
        $tickets = SlaTicket::orderBy('created_at', 'desc')->get();  // Changed from Ticket::
        return view('tickets.index', compact('tickets'));
    }
}