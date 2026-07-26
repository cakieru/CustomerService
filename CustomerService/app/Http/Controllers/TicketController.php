<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\SupportAgent;
use App\Services\SlaCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

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
     * Store a reply to the ticket
     * FIXED: Sets responded_at on first admin reply for SLA tracking
     */
    public function storeReply(Request $request, $ticket_id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $sender = $request->input('sender_type', 'Customer');
        $userId = auth()->id() ?? session('customer_id') ?? 1;

        // 1. Save customer/agent reply to ticket_replies
        \App\Models\TicketReply::create([
            'ticket_id' => $ticket_id,
            'user_id'   => $userId,
            'body'      => $request->input('message'),
        ]);

        $ticket = Ticket::find($ticket_id);

        // 2. Reopen ticket if customer replies while resolved/closed
        if ($ticket && $sender === 'Customer' && in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        // 3. Set first response time if this is the first admin reply
        $adminReplyCount = \App\Models\TicketReply::where('ticket_id', $ticket_id)
            ->whereHas('user', function ($q) {
                $q->where('role', 'admin');
            })
            ->count();

        $updateData = [];

        if ($adminReplyCount === 1 && !$ticket->first_response_at) {
            $updateData['first_response_at'] = now();
            $updateData['response_time_minutes'] = $ticket->created_at->diffInMinutes(now());
        }

        // FIXED: Set responded_at for SLA tracking on first admin reply
        if ($sender !== 'Customer' && !$ticket->responded_at) {
            $updateData['responded_at'] = now();
        }

        if (!empty($updateData)) {
            $ticket->update($updateData);
        }

        // 4. Send auto-reply for customer messages
        if ($sender === 'Customer') {
            \App\Models\TicketReply::create([
                'ticket_id' => $ticket_id,
                'user_id'   => 1, // System user ID
                'body'      => "Thank you for reaching out! We have successfully received your reply for Ticket #{$ticket_id}. An agent will review it shortly.",
            ]);
        }

        // 5. Update SLA metrics
        try {
            SlaCalculator::updateSlaData();
        } catch (\Exception $e) {
            // SLA failed but reply was saved — don't crash the user experience
        }

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
    ? DB::table('support_agents')->where('agent_id', $ticket->agent_id)->first()?->name 
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
        $ticket->resolved_at = $ticket->resolved_at ?? now();

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

        try {
            SlaCalculator::updateSlaData();
        } catch (\Exception $e) {
            // Don't crash
        }

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
        if (!$ticket->resolved_at) {
            $ticket->resolved_at = now();
        }
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

        try {
            SlaCalculator::updateSlaData();
        } catch (\Exception $e) {
            // Don't crash
        }

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