<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Services\SlaCalculator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
   public function home()
{
    // Fetch articles the same way KnowledgeBaseController does for customer portal
    $query = \Illuminate\Support\Facades\DB::table('kb_articles')
        ->where('visibility', 'public');
    
    $dbArticles = $query->get();
    $dbCategories = \Illuminate\Support\Facades\DB::table('article_categories')->get();

    // Format articles collection for the UI (same logic as KnowledgeBaseController)
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
            'visibility' => $article->visibility,
        ];
    });

    return view('CustomerPortal', compact('articles'));
}
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

        return view('customer.CustomerIndex', compact('tickets'));
    }

    /**
     * 2. SHOW SINGLE TICKET DETAILS
     */
    public function show($id)
    {
        $ticket = Ticket::with(['replies.user'])->findOrFail($id);
        
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
            'category'    => 'required|string',
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        $user = User::firstOrCreate(
            ['email' => $request->email],
            ['name' => $request->name, 'password' => bcrypt('password')]
        );

        Session::put('customer_id', $user->id);

        // Determine priority based on category or default
        $priority = 'low';
        $priorityLevel = 'Low';

        $hours = match ($priority) {
            'high'   => 4, 
            'medium' => 8, 
            'low'    => 24, 
            default  => 24,
        };

        $ticket = Ticket::create([
            'ticket_reference' => 'TKT-' . rand(1000, 9999),
            'user_id'          => $user->id,
            'customer_name'    => $request->name,
            'subject'          => $request->subject,
            'description'      => $request->description,
            'issue_description'=> $request->description,
            'category'         => $request->category,
            'priority'         => $priority,
            'priority_level'   => $priorityLevel,
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

        // Update SLA metrics after creating ticket
        SlaCalculator::updateSlaData();

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
            'customer_name'    => $user->name,
            'subject'          => 'Live Chat Session',
            'description'      => 'Active floating bubble conversation thread.',
            'issue_description'=> 'Active floating bubble conversation thread.',
            'category'         => 'Technical Support',
            'priority'         => 'medium',
            'priority_level'   => 'Medium',
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

        if ($customerId && $ticket->user_id != $customerId) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $customerId ?? 1,
            'body'      => $request->body,
        ]);

        // If the ticket was resolved/closed, reopen it when customer replies
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        // Update SLA metrics
        SlaCalculator::updateSlaData();

        return back()->with('success', 'Your reply has been sent.');
    }
}