<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\SlaCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Show all tickets for the current user
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('customer.CustomerInd', compact('tickets'));
    }

    /**
     * Show single ticket details
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        // Security Check
        if ($ticket->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $replies = DB::table('customer_conversations')
            ->where('ticket_id', $id)
            ->orderBy('sent_at', 'asc')
            ->get();

        return view('customer.customerTicket', compact('ticket', 'replies'));
    }

    /**
     * Store a reply to a ticket
     */
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

        // Update ticket status if customer replies
        $ticket = Ticket::find($ticket_id);
        if ($ticket && $sender === 'Customer' && in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        // Send auto-reply for customer messages
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

        // Update SLA metrics
        SlaCalculator::updateSlaData();

        return redirect()->back()->with('success', 'Reply sent successfully!');
    }
}