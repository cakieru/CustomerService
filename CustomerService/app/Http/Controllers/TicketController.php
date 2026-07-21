<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    // A. Ipakita ang lahat ng tickets ng user opo ipakita mo
    public function index()
    {
        // Kukunin ang mga tickets ng kasalukuyang naka-log in na user
        $tickets = DB::table('tickets')
            ->where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('customer.CustomerIndex', compact('tickets'));
    }

    // B. Ipakita ang detalye ng ISANG specific na ticket
    public function show($id)
    {
        // Kukunin ang ticket base sa ID sa URL
        $ticket = DB::table('tickets')->where('id', $id)->first();

        // Security Check: Kung walang ticket o hindi sa kanya ang ticket, block ito
        if (!$ticket || ($ticket->user_id !== auth()->id() && !auth()->user()->is_admin)) {
            abort(403, 'Unauthorized access.');
        }

        // Kukunin ang lahat ng conversations/replies para sa ticket na ito
        $replies = DB::table('customer_conversations')
            ->where('ticket_id', $id)
            ->orderBy('sent_at', 'asc')
            ->get();

        // Ipapasa ang $ticket at $replies sa show.blade.php
        return view('customer.customerTicket', compact('ticket', 'replies'));
    }

    // C. Ang iyong storeReply method para sa pagsagot sa ticket
    public function storeReply(Request $request, $ticket_id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $sender = $request->input('sender_type', 'Customer'); 

        DB::table('customer_conversations')->insert([
            'ticket_id'          => $ticket_id,
            'sender'             => $sender,
            'communication_type' => 'Chat',
            'message'            => $request->input('message'),
            'sent_at'            => now(),
            'created_at'         => now(),
        ]);

        if ($sender === 'Customer') {
            DB::table('customer_conversations')->insert([
                'ticket_id'          => $ticket_id,
                'sender'             => 'System', 
                'communication_type' => 'Chat',
                'message'            => "Thank you for reaching out! We have successfully received your reply for Ticket #{$ticket_id}. An agent will review it shortly.",
                'sent_at'            => now()->addSecond(), 
                'created_at'         => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Reply sent successfully!');
    }
}