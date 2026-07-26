
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupportDesk - Tickets</title>
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

    <!-- Premium Portal Animations Setup -->
    <style>
        body, * {
            font-family: 'Google Sans Flex', sans-serif !important;
        }

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

        /* Force standard layout grids so transforms actually work on table rows */
        .table-grid {
            display: grid;
            grid-template-columns: 0.8fr 2fr 1.5fr 1fr 1fr 1.2fr 1fr;
            align-items: center;
            gap: 16px;
            min-width: 860px;
        }

        .ticket-row {
            opacity: 0;
            will-change: transform, opacity;
            transition:
                background-color 0.4s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.4s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Staggered entry animation exactly matching portal.blade */
        .animate-portal-reveal {
            animation: portalFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: calc(var(--row-index, 0) * 45ms);
        }

        /* Ultra-smooth floating lift effect */
        .ticket-row:hover {
            background-color: #f8fafc !important;
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08), 0 4px 12px -4px rgba(0, 0, 0, 0.04);
            border-color: #e2e8f0;
            z-index: 10;
        }

        * {
            scrollbar-width: auto;
            scrollbar-color: #828282 transparent;
        }
        *::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        *::-webkit-scrollbar-track {
            background: transparent;
        }
        *::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        *::-webkit-scrollbar-thumb:hover {
            background-color: #646464;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex overflow-hidden">

    <!-- Responsive Collapsible Sidebar -->
    <aside class="w-16 sm:w-20 md:w-64 bg-white border-r border-gray-200 flex flex-col justify-between fixed h-full z-30 transition-all duration-300">
        <div>
            <!-- Header Logo -->
            <div class="p-4 md:p-6 border-b border-gray-100 flex items-center justify-center md:justify-start gap-3">
                <div class="hidden md:block overflow-hidden whitespace-nowrap">
                    <h1 class="text-lg font-bold text-gray-900 leading-tight">SupportDesk</h1>
                    <p class="text-[10px] text-gray-400">E-commerce Support</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-2 md:p-4 space-y-1">
                <a href="{{ route('admin.support.dashboard') }}" title="Dashboard" class="flex items-center justify-center md:justify-start gap-3 px-3 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="hidden md:inline whitespace-nowrap">Dashboard</span>
                </a>
                <a href="{{ route('agent') }}" title="Tickets" class="flex items-center justify-center md:justify-start gap-3 px-3 py-3 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg transition-all">
                    <i data-lucide="ticket" class="w-5 h-5 text-blue-600 flex-shrink-0"></i>
                    <span class="hidden md:inline whitespace-nowrap">Tickets</span>
                </a>
                <a href="{{ route('KnowledgeBase') }}" title="Knowledge Base" class="flex items-center justify-center md:justify-start gap-3 px-3 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all">
                    <i data-lucide="book-open" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="hidden md:inline whitespace-nowrap">Knowledge Base</span>
                </a>
                <a href="{{ route('admin.support.reports') }}" title="SLA Reports" class="flex items-center justify-center md:justify-start gap-3 px-3 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="hidden md:inline whitespace-nowrap">SLA Reports</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-2 md:p-4 border-t border-gray-100">
            <a href="{{ route('customer') }}" title="Customer Portal" class="flex items-center justify-center md:justify-start gap-3 px-3 py-3 text-sm font-medium text-purple-700 hover:bg-purple-50 rounded-lg transition-all">
                <i data-lucide="user" class="w-5 h-5 flex-shrink-0"></i>
                <span class="hidden md:inline whitespace-nowrap">Customer Portal</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper (Adjusts padding based on sidebar size) -->
    <div class="flex-1 pl-16 sm:pl-20 md:pl-64 flex flex-col h-screen overflow-hidden relative w-full transition-all duration-300">

        <!-- Responsive Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-20 flex-shrink-0">
            <div class="relative w-48 sm:w-72 md:w-96">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="searchBar" placeholder="Search tickets, customers, articles..." class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
            </div>
            <div class="flex items-center gap-2 sm:gap-4">

                <!-- Anchored Relative Parent Dropdown Holder -->
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

                    <!-- FLOATING NOTIFICATIONS POPUP (Anchored right beneath the bell) -->
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
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold text-xs flex-shrink-0">AD</div>
                    <div class="hidden sm:block">
                        <p class="text-xs font-semibold text-gray-900 leading-tight">Admin User</p>
                        <p class="text-[10px] text-gray-400">Support Manager</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTAINER -->
        <main class="p-4 sm:p-8 flex-1 overflow-y-auto h-[calc(100vh-4rem)]">
            <div class="max-w-7xl w-full mx-auto space-y-6">

                <!-- TITLE HEADER SECTION -->
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-950 tracking-tight">Tickets</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage and track customer support tickets</p>
                </div>

                <!-- FILTERS BLOCK -->
                <div class="bg-white p-4 sm:p-5 border border-gray-200 rounded-xl shadow-sm space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-sm font-bold text-gray-500">Filters:</span>
                            <input type="text" id="filterSearch" placeholder="Search Tickets..." class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 w-full sm:w-auto">

                            <!-- Status Dropdown -->
                            <select id="statusFilter" class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none bg-white font-medium text-gray-700 cursor-pointer">
                                <option value="all">All Status</option>
                                <option value="open">Open</option>
                                <option value="in-progress">In progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>

                            <!-- Priority Dropdown -->
                            <select id="priorityFilter" class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none bg-white font-medium text-gray-700 cursor-pointer">
                                <option value="all">All Priority</option>
                                <option value="critical">Critical</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <button class="flex items-center justify-center gap-2 px-4 py-1.5 text-sm font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition-all text-gray-700 shadow-sm w-full lg:w-auto">
                            <i data-lucide="download" class="w-4 h-4"></i> Export
                        </button>
                    </div>
                    <div id="ticketCount" class="text-xs text-gray-400 font-medium">Showing {{ $tickets->count() }} of {{ $tickets->count() }} tickets</div>
                </div>

                <!-- NEW MODERN FLEXIBLE DATA TABLE LAYOUT (horizontally scrollable on small screens) -->
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm flex flex-col">
                    <div class="overflow-x-auto">
                        <!-- Table Head Row -->
                        <div class="table-grid bg-gray-100 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider py-3.5 px-6">
                            <div>Ticket ID</div>
                            <div>Subject</div>
                            <div>Customer</div>
                            <div>Status</div>
                            <div>Priority</div>
                            <div>Assigned To</div>
                            <div>Created</div>
                        </div>

                        <!-- Table Body Container -->
                        <div id="ticketTableBody" class="divide-y divide-gray-100 text-sm flex flex-col relative bg-white">

                        @forelse($tickets as $index => $ticket)
                            <div class="ticket-row table-grid py-4 px-6 bg-white border-b border-gray-100 cursor-pointer animate-portal-reveal"
                                 data-status="{{ $ticket->status }}"
                                 data-priority="{{ $ticket->priority }}"
                                 data-url="{{ route('admin.support.tickets.show', $ticket) }}"
                                 onclick="window.location=this.dataset.url"
                                 style="--row-index: {{ $index }};">
                                <div class="font-semibold text-blue-600">{{ $ticket->ticket_reference }}</div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $ticket->subject }}</p>
                                    @if($ticket->status === 'open' && in_array($ticket->priority, ['high', 'critical']))
                                    <p class="text-[11px] font-bold text-red-600 mt-0.5">Overdue</p>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-600">
                                            {{ strtoupper(substr($ticket->customer->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 leading-tight">{{ $ticket->customer->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-400">{{ $ticket->customer->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                        {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' :
                                           ($ticket->status === 'in-progress' ? 'bg-yellow-100 text-yellow-700' :
                                           ($ticket->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600')) }}">
                                        {{ $ticket->status }}
                                    </span>
                                </div>
                                <div>
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                        {{ $ticket->priority === 'critical' ? 'bg-red-100 text-red-700' :
                                           ($ticket->priority === 'high' ? 'bg-orange-100 text-orange-700' :
                                           ($ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600')) }}">
                                        {{ $ticket->priority }}
                                    </span>
                                </div>
                                <div>
                                    @if($ticket->agent)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-[10px] font-bold">
                                            {{ strtoupper(substr($ticket->agent->name, 0, 2)) }}
                                        </div>
                                        <span class="text-gray-700 font-medium text-xs">{{ $ticket->agent->name }}</span>
                                    </div>
                                    @else
                                    <span class="text-gray-400 text-xs italic">Unassigned</span>
                                    @endif
                                </div>
                                <div class="text-gray-500 font-medium">{{ $ticket->created_at->format('n/j/Y') }}</div>
                            </div>
                            @empty
                            <div class="py-12 text-center text-gray-400">
                                <p>No tickets found.</p>
                            </div>
                            @endforelse

                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // --- Notifications Open/Close Toggle Control ---
            const toggleBtn = document.getElementById('notiToggle');
            const dropdown = document.getElementById('notiDropdown');

            if (toggleBtn && dropdown) {
                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation(); 
                    dropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!dropdown.contains(e.target) && e.target !== toggleBtn) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            // --- Existing Ticket Filter Rules Engine ---
            const statusFilter = document.getElementById('statusFilter');
            const priorityFilter = document.getElementById('priorityFilter');
            const filterSearch = document.getElementById('filterSearch');
            const searchBar = document.getElementById('searchBar');
            const ticketRows = document.querySelectorAll('.ticket-row');
            const ticketCountDisplay = document.getElementById('ticketCount');

            function filterTickets() {
                const targetStatus = statusFilter.value;
                const targetPriority = priorityFilter.value;
                const searchVal = filterSearch.value.toLowerCase().trim();
                const globalSearchVal = searchBar.value.toLowerCase().trim();

                let visibleIndex = 0;

                ticketRows.forEach((row) => {
                    const rowStatus = row.getAttribute('data-status');
                    const rowPriority = row.getAttribute('data-priority');
                    const textContent = row.textContent.toLowerCase();

                    const matchesStatus = (targetStatus === 'all' || rowStatus === targetStatus);
                    const matchesPriority = (targetPriority === 'all' || rowPriority === targetPriority);
                    const matchesSearch = (!searchVal || textContent.includes(searchVal));
                    const matchesGlobalSearch = (!globalSearchVal || textContent.includes(globalSearchVal));

                    if (matchesStatus && matchesPriority && matchesSearch && matchesGlobalSearch) {
                        row.style.display = 'grid'; 
                        row.style.setProperty('--row-index', visibleIndex);
                        row.classList.remove('animate-portal-reveal');
                        void row.offsetHeight; 
                        row.classList.add('animate-portal-reveal');
                        visibleIndex++;
                    } else {
                        row.style.display = 'none';
                        row.classList.remove('animate-portal-reveal');
                    }
                });

                ticketCountDisplay.textContent = `Showing ${visibleIndex} of ${ticketRows.length} tickets`;
            }

            filterTickets();

            statusFilter.addEventListener('change', filterTickets);
            priorityFilter.addEventListener('change', filterTickets);
            filterSearch.addEventListener('input', filterTickets);
            searchBar.addEventListener('input', filterTickets);
        });
    </script>
</body>
</html>