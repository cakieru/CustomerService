<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\SupportAgent;
use App\Services\SlaCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
     * Show single ticket details (ADMIN VIEW)
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        // Security Check
        if ($ticket->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        // Get replies with user info joined
        $replies = DB::table('customer_conversations')
            ->leftJoin('users', 'customer_conversations.user_id', '=', 'users.id')
            ->where('customer_conversations.ticket_id', $id)
            ->orderBy('customer_conversations.sent_at', 'asc')
            ->select(
                'customer_conversations.*',
                'users.name as user_name',
                'users.role as user_role'
            )
            ->get();

        // Get agents from support_agents table
        $agents = DB::table('support_agents')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('customer.customerTicket', compact('ticket', 'replies', 'agents'));
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
            'user_id'            => auth()->id(),
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

        // Set first response time if this is the first admin reply
        $replyCount = DB::table('customer_conversations')
            ->where('ticket_id', $ticket_id)
            ->where('sender', '!=', 'Customer')
            ->where('sender', '!=', 'System')
            ->count();

        if ($replyCount === 1 && !$ticket->first_response_at) {
            $ticket->update([
                'first_response_at' => now(),
                'response_time_minutes' => $ticket->created_at->diffInMinutes(now()),
            ]);
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

    /**
     * Assign an agent to the ticket
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'agent_id' => 'nullable|integer',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->agent_id = $request->agent_id ?: null;
        $ticket->save();

        $agentName = $request->agent_id 
            ? DB::table('support_agents')->where('id', $request->agent_id)->value('name')
            : null;

        return redirect()
            ->route('admin.support.tickets.show', $id)
            ->with('success', $agentName 
                ? "Ticket assigned to {$agentName}." 
                : 'Agent unassigned.');
    }

    /**
     * Resolve the ticket
     */
    public function resolve($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = 'resolved';
        $ticket->resolved_at = now();

        // Calculate resolution time in minutes
        $ticket->resolution_time_minutes = $ticket->created_at->diffInMinutes(now());

        $ticket->save();

        // Add system note
        DB::table('customer_conversations')->insert([
            'ticket_id'          => $id,
            'sender'             => 'System',
            'communication_type' => 'Status Update',
            'message'            => 'This ticket has been marked as resolved.',
            'sent_at'            => now(),
            'created_at'         => now(),
        ]);

        return redirect()
            ->route('admin.support.tickets.show', $id)
            ->with('success', 'Ticket resolved successfully.');
    }

    /**
     * Close the ticket
     */
    public function close($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = 'closed';
        $ticket->save();

        // Add system note
        DB::table('customer_conversations')->insert([
            'ticket_id'          => $id,
            'sender'             => 'System',
            'communication_type' => 'Status Update',
            'message'            => 'This ticket has been closed.',
            'sent_at'            => now(),
            'created_at'         => now(),
        ]);

        return redirect()
            ->route('admin.support.tickets.show', $id)
            ->with('success', 'Ticket closed successfully.');
    }

    /**
     * Delete the ticket
     */
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);

        // Delete related conversations first
        DB::table('customer_conversations')->where('ticket_id', $id)->delete();

        $ticket->delete();

        return redirect()
            ->route('admin.support.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}