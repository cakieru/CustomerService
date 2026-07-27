<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupportDesk - Ticket #{{ $ticket->ticket_reference }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:wght@100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Google Sans Flex', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        @keyframes detailFadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-detail-reveal {
            opacity: 0;
            animation: detailFadeInUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: calc(var(--panel-index, 0) * 60ms);
        }
        .interactive-card {
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .interactive-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -6px rgba(0, 0, 0, 0.04), 0 3px 8px -2px rgba(0, 0, 0, 0.02);
            border-color: #e2e8f0;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex overflow-hidden">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between fixed h-full z-30">
        <div>
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">SupportDesk</h1>
                <p class="text-xs text-gray-400 mt-1">Admin Support Portal</p>
            </div>
            <nav class="p-4 space-y-1">
                <a href="{{ route('admin.support.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ route('agent') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg transition-all duration-300">
                    <i data-lucide="ticket" class="w-5 h-5 text-blue-600"></i> Tickets
                </a>
                <a href="{{ route('KnowledgeBase') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="book-open" class="w-5 h-5"></i> Knowledge Base
                </a>
                <a href="{{ route('admin.support.reports') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i> SLA Reports
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-gray-100">
            <a href="{{ route('CustomerPortal') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-purple-700 hover:bg-purple-50 rounded-lg transition-all duration-300">
                <i data-lucide="user" class="w-5 h-5"></i> Customer Portal
            </a>
        </div>
    </aside>

    <div class="flex-1 pl-64 flex flex-col h-screen overflow-hidden">

        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 flex-shrink-0">
            <div class="relative w-96">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" placeholder="Search tickets, customers, articles..." class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
            </div>
            <div class="flex items-center gap-4">
                <div class="relative" id="notiWrapper">
                    <button id="notiToggle" class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-all focus:outline-none cursor-pointer">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @php
                            $notifyCount = \App\Models\Ticket::where('status', 'open')->count();
                        @endphp
                        @if($notifyCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </button>
                    <div id="notiDropdown" class="hidden absolute right-0 mt-2 w-72 sm:w-[360px] bg-white border border-gray-200 rounded-xl shadow-2xl z-50 flex flex-col overflow-hidden">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                            <button class="text-xs font-semibold text-blue-600 hover:underline">Mark all as read</button>
                        </div>
                        <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-100">
                            @php
                                $notifications = \App\Models\Ticket::with('customer')->where('status', 'open')->orderBy('created_at', 'desc')->take(5)->get();
                            @endphp
                            @forelse($notifications as $notify)
                                <a href="{{ route('admin.support.tickets.show', $notify->id) }}" class="p-4 hover:bg-gray-50 transition-all flex items-start gap-3 relative block">
                                    <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-700 flex-shrink-0 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($notify->customer->name ?? 'C', 0, 2)) }}
                                    </div>
                                    <div class="flex-1 pr-3">
                                        <p class="text-xs font-bold text-gray-900 truncate">{{ $notify->subject ?? 'New Support Ticket' }}</p>
                                        <p class="text-[11px] text-gray-500 mt-0.5"><span class="text-blue-600 font-semibold">#{{ $notify->ticket_reference ?? 'TKT-'.$notify->id }}</span> by {{ $notify->customer->name ?? 'Guest' }}</p>
                                        <span class="text-[10px] text-gray-400 block mt-1">{{ $notify->created_at->diffForHumans() }}</span>
                                    </div>
                                    <span class="w-2 h-2 bg-blue-600 rounded-full absolute right-4 top-1/2 -translate-y-1/2"></span>
                                </a>
                            @empty
                                <div class="p-6 text-center text-xs text-gray-400">All caught up! No unread notifications.</div>
                            @endforelse
                        </div>
                        <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                            <a href="{{ route('agent') }}" class="w-full inline-flex items-center justify-center gap-2 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                <i data-lucide="list" class="w-3.5 h-3.5"></i> View all notifications
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 pl-2 border-l border-gray-200">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 leading-tight">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-[10px] text-gray-400">Support Administrator</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 flex-1 overflow-y-auto h-[calc(100vh-4rem)]">

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm font-medium flex items-center gap-2 animate-detail-reveal" style="--panel-index: 0;">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    {{ session('success') }}
                </div>
            @endif

            <a href="{{ route('agent') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 text-sm font-medium hover:-translate-x-1 transition-all duration-300 mb-6">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Tickets
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                <div class="lg:col-span-2 space-y-6">

                    <div class="animate-detail-reveal bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4" style="--panel-index: 0;">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded">
                                    #{{ $ticket->ticket_reference }}
                                </span>

                                @php
                                    $statusColors = [
                                        'open' => 'bg-emerald-50 text-emerald-600',
                                        'in-progress' => 'bg-amber-50 text-amber-600',
                                        'resolved' => 'bg-blue-50 text-blue-600',
                                        'closed' => 'bg-gray-100 text-gray-600',
                                    ];
                                    $statusClass = $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="text-xs font-semibold px-2.5 py-1 rounded uppercase {{ $statusClass }}">
                                    {{ $ticket->status }}
                                </span>

                                @php
                                    $priorityColors = [
                                        'high' => 'bg-red-50 text-red-600',
                                        'medium' => 'bg-amber-50 text-amber-600',
                                        'low' => 'bg-blue-50 text-blue-600',
                                    ];
                                    $priorityClass = $priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="text-xs font-semibold px-2.5 py-1 rounded uppercase {{ $priorityClass }}">
                                    {{ $ticket->priority }} Priority
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($ticket->status !== 'resolved' && $ticket->status !== 'closed')
                                    <form action="{{ route('admin.support.tickets.resolve', $ticket) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-all">
                                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Resolve
                                        </button>
                                    </form>
                                @endif

                                @if($ticket->status !== 'closed')
                                    <form action="{{ route('admin.support.tickets.close', $ticket) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">
                                            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Close
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $ticket->subject }}</h2>
                            <div class="p-4 bg-gray-50 border border-gray-100 rounded-lg text-sm text-gray-700 leading-relaxed">
                                {{ $ticket->description }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-md">
                                <i data-lucide="tag" class="w-3 h-3"></i> {{ $ticket->category ?? 'General' }}
                            </span>
                        </div>
                    </div>

                    <div class="animate-detail-reveal bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden" style="--panel-index: 1;">
                        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                <i data-lucide="message-square" class="w-4 h-4 text-blue-600"></i>
                                Activity & Replies ({{ $ticket->replies->count() }})
                            </h3>
                        </div>

                        <div class="p-6 space-y-6 flex flex-col gap-4">
                            @forelse($ticket->replies as $reply)
                                @php
                                    $isAdmin = $reply->user && $reply->user->role === 'admin';
                                    $isSystem = $reply->sender === 'System' || !$reply->user;
                                    $isCurrentUser = $reply->user_id == auth()->id();
                                    $displayName = $reply->user->name ?? $reply->sender ?? 'Unknown';
                                @endphp

                                @if($isSystem)
                                    <div class="flex justify-center">
                                        <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                                            {{ $reply->body }}
                                        </span>
                                    </div>
                                @else
                                    <div class="flex gap-4 {{ $isAdmin ? 'flex-row-reverse' : '' }}">
                                        <div class="w-9 h-9 {{ $isAdmin ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} font-semibold rounded-full flex-shrink-0 flex items-center justify-center text-xs shadow-sm">
                                            {{ strtoupper(substr($displayName, 0, 2)) }}
                                        </div>

                                        <div class="space-y-1 flex-1 max-w-[80%] {{ $isAdmin ? 'text-right' : '' }}">
                                            <div class="flex items-baseline gap-2 {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                                                <h5 class="font-semibold text-sm text-gray-900">{{ $displayName }}</h5>
                                                @if($isAdmin)
                                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded">Admin</span>
                                                @endif
                                                <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>

                                            <div class="inline-block p-4 rounded-xl text-sm leading-relaxed text-left shadow-sm {{ $isAdmin ? 'bg-blue-600 text-white border border-blue-700' : 'bg-gray-50 text-gray-700 border border-gray-100' }}">
                                                {{ $reply->body }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div class="text-center text-gray-400 py-8">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto stroke-1 mb-2"></i>
                                    <p class="text-sm">No replies yet. Be the first to respond!</p>
                                </div>
                            @endforelse
                        </div>

                        <form action="{{ route('admin.support.tickets.reply', $ticket) }}" method="POST" class="p-4 border-t border-gray-100 bg-gray-50/50 space-y-3">
                            @csrf
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex-shrink-0 flex items-center justify-center font-semibold text-xs mt-1 shadow-sm">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                                </div>
                                <div class="w-full">
                                    <textarea name="body" rows="3" placeholder="Type your reply here..." required
                                        class="w-full p-3 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none shadow-sm transition-all @error('body') border-red-500 @enderror"></textarea>
                                    @error('body')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-between pl-11">
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-600 cursor-pointer select-none group">
                                    <input type="checkbox" name="resolve_ticket" class="rounded text-blue-600 border-gray-300 focus:ring-blue-500 w-4 h-4 transition-all">
                                    <span class="group-hover:text-gray-900 transition-colors">Mark ticket as resolved</span>
                                </label>

                                <button type="submit" class="flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow active:scale-[0.98] transition-all">
                                    Send Reply <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">

                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4" style="--panel-index: 2;">
                        <h4 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-gray-500"></i> Customer Profile
                        </h4>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center font-bold text-sm shadow-sm">
                                {{ strtoupper(substr($ticket->customer->name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-bold text-sm text-gray-900">{{ $ticket->customer->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $ticket->customer->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <button
                            id="openCustomerProfile"
                            type="button"
                            class="block w-full text-center py-2 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg active:scale-[0.98] transition-all">
                            View Customer Profile
                        </button>
                    </div>

                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4" style="--panel-index: 3;">
                        <h4 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                            <i data-lucide="sliders" class="w-4 h-4 text-gray-500"></i> Ticket Settings
                        </h4>

                        <!-- Assign Agent -->
                        <form action="{{ route('admin.support.tickets.assign', $ticket) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 block mb-1">Assign to Agent</label>
                                <div class="flex gap-2">
                                <select name="agent_id" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                                    <option value="">-- Unassigned --</option>
                                    @foreach(\App\Models\User::where('role', 'agent')->get() as $agent)
                                        <option value="{{ $agent->id }}" {{ $ticket->agent_id == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-blue-600 border border-blue-200 hover:bg-blue-50 rounded-lg transition-colors">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Update Priority -->
                        <form action="{{ route('admin.support.tickets.priority', $ticket) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 block mb-1">Priority Level</label>
                                <div class="flex gap-2">
                                    <select name="priority" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                                        <option value="critical" {{ $ticket->priority === 'critical' ? 'selected' : '' }}>Critical (4h SLA)</option>
                                        <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High (8h SLA)</option>
                                        <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium (16h SLA)</option>
                                        <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low (24h SLA)</option>
                                    </select>
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-emerald-600 border border-emerald-200 hover:bg-emerald-50 rounded-lg transition-colors">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Admin Notepad -->
                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4" style="--panel-index: 4;">
                        <h4 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4 text-gray-500"></i> Admin Notepad
                        </h4>
                        
                        <form action="{{ route('admin.support.tickets.notes', $ticket) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <textarea 
                                    name="admin_notes" 
                                    rows="5" 
                                    placeholder="Write internal notes, next steps, or reminders about this ticket…"
                                    class="w-full p-3 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none transition-all"
                                >{{ old('admin_notes', $ticket->admin_notes) }}</textarea>
                            </div>
                            <button type="submit" class="w-full text-center py-2 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg active:scale-[0.98] transition-all">
                                Save Notes
                            </button>
                        </form>
                    </div>

                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4" style="--panel-index: 5;">
                        <h4 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i> SLA & Metrics
                        </h4>

                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-gray-400 font-medium block">First Response Time</span>
                                <p class="font-semibold text-gray-800 mt-0.5">
                                    @if($ticket->replies->where('user.role', 'admin')->count() > 0)
                                        <span class="text-emerald-600 inline-flex items-center gap-1">
                                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                            Responded
                                        </span>
                                    @else
                                        <span class="text-amber-600 inline-flex items-center gap-1">
                                            <i data-lucide="hourglass" class="w-3.5 h-3.5"></i> Awaiting response
                                        </span>
                                    @endif
                                </p>
                            </div>

                            <div class="border-t border-gray-100 pt-2">
                                <span class="text-gray-400 font-medium block">Resolution Time</span>
                                <p class="font-semibold text-gray-800 mt-0.5">
                                    @if($ticket->status === 'resolved')
                                        <span class="text-emerald-600 inline-flex items-center gap-1">
                                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                            Resolved
                                        </span>
                                    @else
                                        <span class="text-amber-600 inline-flex items-center gap-1">
                                            <i data-lucide="hourglass" class="w-3.5 h-3.5"></i> Not resolved yet
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-3" style="--panel-index: 6;">
                        <h4 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i> Timeline
                        </h4>
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex items-center gap-2">
                                <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-gray-400"></i>
                                <span>Created: {{ $ticket->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-gray-400"></i>
                                <span>Due Date: {{ $ticket->due_date ? $ticket->due_date->format('M d, Y H:i') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Panel -->
                    @if($ticket->attachments && $ticket->attachments->count() > 0)
                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-3" style="--panel-index: 6;">
                        <h4 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                            <i data-lucide="paperclip" class="w-4 h-4 text-gray-500"></i> Attachments ({{ $ticket->attachments->count() }})
                        </h4>
                        <div class="space-y-2">
                            @foreach($ticket->attachments as $file)
                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="flex items-center justify-between border border-gray-100 rounded-lg p-2.5 bg-gray-50/50 hover:bg-gray-100/70 transition-colors group">
                                <div class="flex items-center gap-2 text-xs min-w-0">
                                    <div class="w-8 h-8 bg-white border border-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 text-blue-500 shadow-sm">
                                        <i data-lucide="file" class="w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 truncate">{{ $file->filename }}</p>
                                    </div>
                                </div>
                                <i data-lucide="external-link" class="w-4 h-4 text-gray-400 group-hover:text-gray-700 flex-shrink-0 ml-2"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="animate-detail-reveal interactive-card bg-white border border-red-100 rounded-xl p-5 shadow-sm space-y-3" style="--panel-index: 7;">
                        <h4 class="font-bold text-red-600 text-sm flex items-center gap-2">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Danger Zone
                        </h4>
                        <form action="{{ route('admin.support.tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg active:scale-[0.98] transition-all">
                                Delete Ticket
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </main>
    </div>

<!-- Customer Profile Modal -->

<div id="customerModal"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-[9999]">

    <div id="customerCard"
        class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 transform scale-95 opacity-0 transition-all duration-300">

        <div class="flex justify-between items-center border-b p-6">

            <div class="flex items-center gap-4">

                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-xl font-bold">
                    PD
                </div>

                <div>

                    <h2 class="text-2xl font-bold">
                        Panda Decoco
                    </h2>

                    <p class="text-gray-400">
                        Customer Profile
                    </p>

                </div>

            </div>

            <button
                id="closeCustomerProfile"
                type="button"
                class="w-10 h-10 rounded-full hover:bg-gray-100 transition">

                ✕

            </button>

        </div>

        <div class="grid md:grid-cols-2 gap-5 p-6">

            <div class="border rounded-xl p-5">
                <p class="text-xs text-gray-400 uppercase">First Name</p>
                <h3 class="font-semibold mt-1">Panda</h3>
            </div>

            <div class="border rounded-xl p-5">
                <p class="text-xs text-gray-400 uppercase">Last Name</p>
                <h3 class="font-semibold mt-1">Decoco</h3>
            </div>

            <div class="border rounded-xl p-5">
                <p class="text-xs text-gray-400 uppercase">Email</p>
                <h3 class="font-semibold mt-1">panda@gmail.com</h3>
            </div>

            <div class="border rounded-xl p-5">
                <p class="text-xs text-gray-400 uppercase">Phone Number</p>
                <h3 class="font-semibold mt-1">09658852674</h3>
            </div>

            <div class="border rounded-xl p-5">
                <p class="text-xs text-gray-400 uppercase">Member Since</p>
                <h3 class="font-semibold mt-1">Jul 26, 2026</h3>
            </div>

            <div class="border rounded-xl p-5">
                <p class="text-xs text-gray-400 uppercase">Verification</p>

                <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-4 py-2 mt-2">

                    ✔ Verified

                </span>

            </div>

        </div>

        <div class="border-t p-5 flex justify-end">

            <button
                id="closeCustomerProfile2"
                type="button"
                class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">

                Close

            </button>

        </div>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const openBtn = document.getElementById("openCustomerProfile");
    const closeBtn = document.getElementById("closeCustomerProfile");
    const closeBtn2 = document.getElementById("closeCustomerProfile2");
    const modal = document.getElementById("customerModal");
    const card = document.getElementById("customerCard");

    if (!openBtn || !modal || !card) return;

    function openCustomerModal() {
        modal.classList.remove("hidden");
        modal.classList.add("flex");

        setTimeout(() => {
            card.classList.remove("opacity-0", "scale-95");
            card.classList.add("opacity-100", "scale-100");
        }, 10);
    }

    function closeCustomerModal() {
        card.classList.remove("opacity-100", "scale-100");
        card.classList.add("opacity-0", "scale-95");

        setTimeout(() => {
            modal.classList.remove("flex");
            modal.classList.add("hidden");
        }, 250);
    }

    openBtn.addEventListener("click", openCustomerModal);

    if (closeBtn)
        closeBtn.addEventListener("click", closeCustomerModal);

    if (closeBtn2)
        closeBtn2.addEventListener("click", closeCustomerModal);

    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            closeCustomerModal();
        }
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && !modal.classList.contains("hidden")) {
            closeCustomerModal();
        }
    });

});
</script>
</body>
</html>