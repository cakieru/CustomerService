<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    public function home()
    {
        return view('CustomerPortal');
    }
    
    /**
     * 1. DISPLAY ALL TICKETS
     */
    public function index(Request $request)
    {
        $customerId = Session::get('customer_id');
        
        $query = Ticket::query();

        if ($customerId) {
            $query->where('user_id', $customerId);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $tickets = $query->latest()->get();

        return view('customer.index', compact('tickets'));
    }

    /**
     * 2. SHOW SINGLE TICKET DETAILS (✨ Fixed: This prevents your current error screen!)
     */
    public function show($id)
    {
        // Fetch the ticket with its replies, or throw a 404 if not found
        $ticket = Ticket::with(['replies.user'])->findOrFail($id);
        
        // Security check: ensure the current session customer actually owns this ticket
        $customerId = Session::get('customer_id');
        if ($customerId && $ticket->user_id != $customerId) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        return view('customer.show', compact('ticket'));
    }

    /**
     * 3. SHOW TICKET CREATION FORM
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * 4. SUBMIT AND SAVE NEW TICKET
     */
    
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'category'    => 'required',
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        $priority = 'low';

        $hours = match ($priority) {
            'high'   => 4, 
            'medium' => 8, 
            'low'    => 24, 
            default  => 24,
        };

        $user = User::firstOrCreate(
            ['email' => $request->email],
            ['name' => $request->name, 'password' => bcrypt('password')]
        );

        Session::put('customer_id', $user->id);

        $ticket = Ticket::create([
            'ticket_reference' => 'TKT-' . rand(1000, 9999),
            'user_id'          => $user->id,
            'subject'          => $request->subject,
            'description'      => $request->description,
            'category'         => $request->category,
            'priority'         => $priority,
            'status'           => 'open',
            'due_date'         => Carbon::now()->addHours($hours),
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-attachments', 'public');
                $ticket->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path'     => $path,
                ]);
            }
        }

        return redirect('/tickets')->with('success', 'Ticket created successfully!');
    }

    /**
     * 5. START LIVE CHAT LOGIC
     */
    public function startLiveChat(Request $request)
    {
        $userId = Session::get('customer_id');
        
        if (!$userId) {
            return response()->json([
                'error' => 'Please submit a ticket first to activate Live Chat.'
            ], 403);
        }

        $user = User::find($userId);

        $ticket = Ticket::where('user_id', $user->id)
                        ->where('subject', 'Live Chat Session')
                        ->first() 
                  ?? Ticket::create([
            'ticket_reference' => 'CHAT-' . rand(1000, 9999),
            'user_id'          => $user->id,
            'subject'          => 'Live Chat Session',
            'description'      => 'Active floating bubble conversation thread.',
            'category'         => 'Technical Support',
            'priority'         => 'medium',
            'status'           => 'open',
            'due_date'         => now()->addHours(8),
        ]);

        return response()->json([
            'ticket_id' => $ticket->id,
            'user_id'   => $user->id
        ]);
    }

    /**
     * 6. SEND MESSAGE & AUTOMATIC BOT REPLY
     */
    public function sendLiveChatMessage(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'user_id'   => 'required|exists:users,id',
            'body'      => 'required|string',
        ]);

        $reply = TicketReply::create([
            'ticket_id' => $request->ticket_id,
            'user_id'   => $request->user_id,
            'body'      => $request->body,
        ]);

        $admin = User::where('role', 'admin')->first() ?? User::find(1);

        if ($admin) {
            TicketReply::create([
                'ticket_id' => $request->ticket_id,
                'user_id'   => $admin->id,
                'body'      => 'We are currently reviewing your ticket details. Please hold on for a moment while we connect you with a support specialist.'
            ]);
        }

        return response()->json(['success' => true, 'reply' => $reply]);
    }

    /**
     * SUBMIT TICKET REPLY (Customer)
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $customerId = Session::get('customer_id');

        // Security check: ensure the user owns this ticket
        if ($customerId && $ticket->user_id != $customerId) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $customerId ?? 1, // Fallback to 1 if session expired during testing
            'body'      => $request->body,
        ]);

        // If the ticket was resolved/closed, reopen it when customer replies
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Your reply has been sent.');
    }
    
}