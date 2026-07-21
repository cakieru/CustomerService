<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tickets - SupportDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 h-screen flex overflow-hidden">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between flex-shrink-0 h-full">
        <div>
            <div class="px-6 pt-8 pb-6">
                <h1 class="text-xl font-bold text-gray-900">SupportDesk</h1>
                <p class="text-sm text-gray-500 mt-1">E-commerce Support</p>
            </div>
            <nav class="px-4 space-y-1">
                <a href="{{ route('admin.support.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.support.tickets.index') }}" class="flex items-center space-x-3 px-3 py-2.5 bg-blue-50 text-blue-700 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    <span>Tickets</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 662 6l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                    </svg>
                    <span>Knowledge Base</span>
                </a>
                <a href="{{ route('admin.support.reports') }}" class="flex items-center space-x-3 px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>SLA Reports</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-gray-200">
            <a href="{{ route('customer.home') }}" class="flex items-center space-x-3 px-3 py-2.5 text-blue-600 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Customer Portal</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-full bg-white relative">
        <header class="h-16 border-b border-gray-200 bg-white flex items-center justify-between px-8 flex-shrink-0">
            <h2 class="text-lg font-bold text-gray-800">Ticket Management</h2>
            <div class="flex items-center space-x-4">
                <div class="h-9 w-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm">AD</div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
            <div class="max-w-7xl mx-auto space-y-6">
                
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                    <form method="GET" action="{{ route('admin.support.tickets.index') }}" class="flex w-full gap-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID or subject..." class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        
                        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none bg-white">
                            <option value="all">All Statuses</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>

                        <select name="priority" class="border border-gray-300 rounded-lg px-4 py-2 text-sm outline-none bg-white">
                            <option value="all">All Priorities</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                        
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">Filter</button>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="p-4">Reference</th>
                                <th class="p-4">Customer</th>
                                <th class="p-4">Subject</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Priority</th>
                                <th class="p-4">Date</th>
                                <th class="p-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 text-sm font-medium text-gray-900">{{ $ticket->ticket_reference }}</td>
                                    <td class="p-4 text-sm text-gray-600">{{ $ticket->customer->name ?? 'Unknown' }}</td>
                                    <td class="p-4 text-sm font-medium text-gray-900">{{ Str::limit($ticket->subject, 40) }}</td>
                                    <td class="p-4 text-sm">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize 
                                            {{ $ticket->status === 'open' ? 'bg-blue-50 text-blue-600' : '' }}
                                            {{ $ticket->status === 'in-progress' ? 'bg-yellow-50 text-yellow-600' : '' }}
                                            {{ $ticket->status === 'resolved' ? 'bg-green-50 text-green-600' : '' }}
                                            {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-600' : '' }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm">
                                        <span class="px-2.5 py-1 rounded text-xs font-semibold capitalize 
                                            {{ $ticket->priority === 'high' || $ticket->priority === 'critical' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-gray-500">{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('admin.support.tickets.show', $ticket->id) }}" class="text-sm font-medium text-blue-600 hover:underline">Manage &rarr;</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-500 text-sm">No tickets found matching your criteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tickets->hasPages())
                    <div class="mt-4">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>