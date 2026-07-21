<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupportDesk Dashboard</title>
    <!-- Google Sans Flex Font -->
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
    <!-- Lucide Icons & Alpine.js -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Premium Animations Setup from Agent Blade -->
    <style>
        @keyframes portalFadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-portal-reveal {
            animation: portalFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: calc(var(--row-index, 0) * 45ms);
        }

        /* Hover lift effect matching agent.blade.php */
        .dash-card-hover {
            will-change: transform, opacity;
            transition: 
                background-color 0.4s cubic-bezier(0.16, 1, 0.3, 1), 
                transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), 
                box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dash-card-hover:hover {
            background-color: #f8fafc !important;
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08), 0 4px 12px -4px rgba(0, 0, 0, 0.04);
            border-color: #e2e8f0;
            z-index: 10;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex overflow-hidden">

    <!-- SIDEBAR (100% Identical to agent.blade.php) -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between fixed h-full z-30">
        <div>
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    SupportDesk
                </h1>
                <p class="text-xs text-gray-400 mt-1">E-commerce Support</p>
            </div>
            <nav class="p-4 space-y-1">
                <a href="{{ route('admin.support.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg transition-all duration-300">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 text-blue-600"></i> Dashboard
                </a>
                <a href="{{ route('agent') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="ticket" class="w-5 h-5"></i> Tickets
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
                <i data-lucide="user" class="w-5 h-5"></i>
                Customer Portal
            </a>
        </div>
    </aside>

    <!-- RIGHT CONTENT AREA -->
    <div class="flex-1 pl-64 flex flex-col h-screen overflow-hidden relative">
        
        <!-- STICKY HEADER NAVBAR (100% Identical to agent.blade.php) -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 flex-shrink-0">
            <div class="relative w-96">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <form action="{{ route('admin.support.tickets.index') }}" method="GET">
                    <input type="text" name="search" placeholder="Search tickets, customers, articles..." class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                </form>
            </div>
            <div class="flex items-center gap-4">
                
                @php
                    $notifications = \App\Models\Ticket::latest()->take(5)->get();
                @endphp

                <!-- Anchored Relative Parent Dropdown Holder -->
                <div x-data="{ notificationsOpen: false }" class="relative">
                    <button @click="notificationsOpen = !notificationsOpen" class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-all focus:outline-none cursor-pointer">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @if(count($notifications) > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </button>

                    <!-- FLOATING NOTIFICATIONS POPUP (Anchored right beneath the bell) -->
                    <div x-show="notificationsOpen" @click.outside="notificationsOpen = false" x-transition class="absolute right-0 mt-2 w-[360px] bg-white border border-gray-200 rounded-xl shadow-2xl z-50 flex flex-col overflow-hidden" style="display: none;">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                            <button class="text-xs font-semibold text-blue-600 hover:underline">Mark all as read</button>
                        </div>
                        <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-100">
                            @forelse($notifications as $notify)
                                <a href="{{ route('admin.support.tickets.show', $notify->id) }}" class="p-4 hover:bg-gray-50 transition-all flex items-start gap-3 relative block">
                                    <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-700 flex-shrink-0 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($notify->customer->name ?? $notify->customer_name ?? 'C', 0, 2)) }}
                                    </div>
                                    <div class="flex-1 pr-3">
                                        <p class="text-xs font-bold text-gray-900 truncate">{{ $notify->subject ?? 'New Support Ticket' }}</p>
                                        <p class="text-[11px] text-gray-500 mt-0.5"><span class="text-blue-600 font-semibold">#{{ $notify->ticket_reference ?? 'TKT-'.$notify->id }}</span> by {{ $notify->customer->name ?? $notify->customer_name ?? 'Guest' }}</p>
                                        <span class="text-[10px] text-gray-400 block mt-1">{{ $notify->created_at->diffForHumans() }}</span>
                                    </div>
                                    <span class="w-2 h-2 bg-blue-600 rounded-full absolute right-4 top-1/2 -translate-y-1/2"></span>
                                </a>
                            @empty
                                <div class="p-6 text-center text-xs text-gray-400">All caught up! No unread notifications.</div>
                            @endforelse
                        </div>
                        <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                            <a href="{{ route('admin.support.tickets.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                <i data-lucide="list" class="w-3.5 h-3.5"></i> View all notifications
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pl-2 border-l border-gray-200">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold text-xs">AD</div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 leading-tight">Admin User</p>
                        <p class="text-[10px] text-gray-400">Support Manager</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTAINER -->
        <main class="p-8 flex-1 overflow-y-auto h-[calc(100vh-4rem)] hide-scrollbar">
            <div class="space-y-6">
                
                @if(session('success'))
                    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-xs font-medium shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif
                
                <div>
                    <h2 class="text-3xl font-bold text-gray-950 tracking-tight">DASHBOARD</h2>
                    <p class="text-sm text-gray-500 mt-1">Overview of support operations and dynamic real-time live feed indicators</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div class="dash-card-hover bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Open Tickets</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1">{{ $summary['openTickets'] }}</h3>
                        </div>
                        <div class="h-12 w-12 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-500">
                            <i data-lucide="ticket" class="w-6 h-6 text-indigo-600"></i>
                        </div>
                    </div>
                    <div class="dash-card-hover bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">In Progress</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1">{{ $summary['inProgress'] }}</h3>
                        </div>
                        <div class="h-12 w-12 bg-yellow-50 rounded-lg flex items-center justify-center text-yellow-500">
                            <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="dash-card-hover bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Resolved Today</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1">{{ $summary['resolvedToday'] }}</h3>
                        </div>
                        <div class="h-12 w-12 bg-green-50 rounded-lg flex items-center justify-center text-green-500">
                            <i data-lucide="check-circle-2" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>
                    <div class="dash-card-hover bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Critical Priority</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1">{{ $summary['criticalPriority'] }}</h3>
                        </div>
                        <div class="h-12 w-12 bg-red-50 rounded-lg flex items-center justify-center text-red-500">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900">Recent Tickets</h3>
                            <a href="{{ route('admin.support.tickets.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">View all</a>
                        </div>
                        <div class="divide-y divide-gray-100 flex-1">
                            @forelse($recentTickets as $ticket)
                                <a href="{{ route('admin.support.tickets.show', $ticket->id) }}" class="dash-card-hover block px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="space-y-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-bold text-gray-900">{{ $ticket->ticket_reference }}</span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold capitalize 
                                                    {{ $ticket->priority === 'critical' || $ticket->priority === 'high' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $ticket->priority }}
                                                </span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-900 truncate max-w-sm">{{ $ticket->subject }}</p>
                                            <p class="text-xs text-gray-500">Client: {{ $ticket->customer->name ?? 'Guest Client' }}</p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded text-xs font-semibold capitalize 
                                            {{ $ticket->status === 'open' ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-600' }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center text-sm text-gray-400">No active recent support tickets found.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900">SLA Alerts</h3>
                            <span class="text-xs bg-red-100 text-red-800 font-bold px-2 py-1 rounded-full">{{ count($slaAlerts) }} Breach Overdue</span>
                        </div>
                        <div class="divide-y divide-gray-100 flex-1">
                            @forelse($slaAlerts as $alert)
                                <a href="{{ route('admin.support.tickets.show', $alert->id) }}" class="dash-card-hover flex px-6 py-4 space-x-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0 mt-0.5 text-red-500">
                                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900 truncate max-w-xs">{{ $alert->subject }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Ref: <span class="font-semibold text-gray-700">{{ $alert->ticket_reference }}</span> • Resolution Deadline Breached</p>
                                        <p class="text-xs text-gray-400 mt-1">Assigned Agent: <span class="text-gray-600 font-medium">{{ $alert->agent->name ?? 'Unassigned Tier 1' }}</span></p>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center text-sm text-gray-400 flex flex-col items-center justify-center h-full">
                                    <i data-lucide="check-circle" class="w-8 h-8 text-green-500 mb-2"></i>
                                    <p class="font-medium text-gray-600">Great job! No pending SLA breaches.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <a href="/tickets" class="dash-card-hover flex items-center space-x-4 p-4 border border-gray-100 hover:border-blue-200 hover:bg-blue-50/30 rounded-xl transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0 group-hover:bg-blue-100 transition-colors">
                                <i data-lucide="ticket" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Create New Ticket</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Manual ticket entry</p>
                            </div>
                        </a>

                        <a href="/knowledge-base" class="dash-card-hover flex items-center space-x-4 p-4 border border-gray-100 hover:border-purple-200 hover:bg-purple-50/30 rounded-xl transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0 group-hover:bg-purple-100 transition-colors">
                                <i data-lucide="book-open" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Browse help articles</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Knowledge base</p>
                            </div>
                        </a>

                        <a href="/sla-reports" class="dash-card-hover flex items-center space-x-4 p-4 border border-gray-100 hover:border-green-200 hover:bg-green-50/30 rounded-xl transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 shrink-0 group-hover:bg-green-100 transition-colors">
                                <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">View SLA reports</h4>
                                <p class="text-xs text-gray-500 mt-0.5">performance metrics</p>
                            </div>
                        </a>

                    </div>
                </div>
            </div>
        </div>
        
    </main>

    <!-- LUCIDE ICONS INITIALIZATION ENGINE -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>