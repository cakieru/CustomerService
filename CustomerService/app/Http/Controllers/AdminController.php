<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Services\SlaCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard
     */
    public function index() 
    {
        try {
            SlaCalculator::updateSlaData();
        } catch (\Exception $e) {
            \Log::error('SLA Update failed on dashboard: ' . $e->getMessage());
        }

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

        $notifications = Ticket::with('customer')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('summary', 'recentTickets', 'slaAlerts', 'notifications'));
    }

    /**
     * Show a Single Ticket (Agent View)
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['customer', 'agent', 'replies.user']);
        $admins = User::where('role', 'admin')->get();

        $notifications = Ticket::with('customer')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

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

        return view('admin.tickets.TicketsIndex', compact('tickets', 'notifications'));
    }

    /**
     * Agent Portal - Tickets List View
     */
    public function agentTickets(Request $request)
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

        $tickets = $query->orderBy('created_at', 'desc')->get();

        $notifications = Ticket::with('customer')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.agent', compact('tickets', 'notifications'));
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|in:open,in-progress,resolved,closed']);
        $ticket->update(['status' => $request->status]);

        try {
            SlaCalculator::updateSlaData();
        } catch (\Exception $e) {
            \Log::error('SLA Update failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Ticket status updated successfully.');
    }

    /**
     * Assign an agent to the ticket
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate(['agent_id' => 'nullable|exists:users,id']);

        $ticket->update(['agent_id' => $request->agent_id]);

        $agentName = $ticket->fresh()->agent ? $ticket->fresh()->agent->name : null;

        return redirect()
            ->route('admin.support.tickets.show', $ticket)
            ->with('success', $agentName 
                ? "Ticket assigned to {$agentName}." 
                : 'Agent unassigned.');
    }

    /**
     * Post a reply to the ticket
     */
    public function reply(Request $request, Ticket $ticket)
{
    $request->validate(['body' => 'required|string']);

    TicketReply::create([
        'ticket_id' => $ticket->id,
        'user_id'   => Auth::id() ?? $ticket->agent_id,
        'body'      => $request->body,
    ]);

    if ($ticket->status === 'open') {
        $ticket->update(['status' => 'in-progress']);
    }

    if ($request->has('resolve_ticket')) {
        $ticket->update(['status' => 'resolved']);
    }

    try {
        SlaCalculator::updateSlaData();
    } catch (\Exception $e) {
        \Log::error('SLA Update failed: ' . $e->getMessage());
    }

    return redirect()
        ->route('admin.support.tickets.show', $ticket)
        ->with('success', 'Reply posted successfully.');
}

    /**
     * Resolve the ticket
     */
    public function resolve(Ticket $ticket)
    {
        $ticket->update(['status' => 'resolved']);

        return redirect()
            ->route('admin.support.tickets.show', $ticket)
            ->with('success', 'Ticket resolved successfully.');
    }

    /**
     * Close the ticket
     */
    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => 'closed']);

        return redirect()
            ->route('admin.support.tickets.show', $ticket)
            ->with('success', 'Ticket closed successfully.');
    }

    /**
     * Delete the ticket
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->replies()->delete();
        $ticket->delete();

        return redirect()
            ->route('agent')
            ->with('success', 'Ticket deleted successfully.');
    }

    /**
     * Show customer profile
     */
    public function showCustomer(User $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    public function reports()
    {
        try {
            SlaCalculator::updateSlaData();
        } catch (\Exception $e) {
            \Log::error('SLA Update failed on reports: ' . $e->getMessage());
        }

        $totalResolved = Ticket::where('status', 'resolved')->count();
        $notifications = Ticket::with('customer')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.reports', compact('totalResolved', 'notifications'));
    }

    /**
 * Legacy method for web.php route compatibility
 */
public function assignAgent(Request $request, $ticket)
{
    $request->validate([
        'agent_id' => 'nullable|exists:users,id'
    ]);

    $ticket = \App\Models\Ticket::findOrFail($ticket);
    $ticket->update([
        'agent_id' => $request->agent_id
    ]);

    return back()->with('success', 'Agent assigned successfully.');
}

    /**
     * Save admin-only notes for the ticket
     */
    public function updateNotes(Request $request, Ticket $ticket)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $ticket->update([
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()
            ->route('admin.support.tickets.show', $ticket)
            ->with('success', 'Admin notes saved successfully.');
    }

    

    /**
         * Update ticket priority and recalculate SLA due date
         */
            /**
     * Update ticket priority and recalculate SLA due date
     */
    public function updatePriority(Request $request, Ticket $ticket)
    {
        $request->validate([
            'priority' => 'required|in:critical,high,medium,low',
        ]);

        $priority = strtolower($request->priority);
        $priorityLevel = ucfirst($priority);

        $hours = match ($priority) {
            'critical' => 4,
            'high'     => 8,
            'medium'   => 16,
            'low'      => 24,
            default    => 24,
        };

        $ticket->update([
            'priority'       => $priority,
            'priority_level' => $priorityLevel,
            'due_date'       => now()->addHours($hours),
        ]);

        try {
            SlaCalculator::updateSlaData();
        } catch (\Exception $e) {
            \Log::error('SLA Update failed after priority change: ' . $e->getMessage());
        }

        return back()->with('success', "Priority updated to {$priorityLevel}. Due date set to " . now()->addHours($hours)->format('M d, Y g:i A') . ".");
    }
}