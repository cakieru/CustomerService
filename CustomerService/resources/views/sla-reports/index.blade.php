<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SupportDesk - SLA Reports</title>
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
        @keyframes portalFadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-portal-reveal {
            animation: portalFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: calc(var(--row-index, 0) * 45ms);
        }
        .dash-card-hover {
            will-change: transform, opacity;
            transition: background-color 0.4s cubic-bezier(0.16, 1, 0.3, 1), 
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
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .chart-tooltip {
            position: absolute;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            pointer-events: none;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity 0.2s ease, transform 0.2s ease;
            z-index: 50;
            min-width: 140px;
        }
        .chart-tooltip.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .chart-tooltip::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid white;
        }
        .priority-bar {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .priority-bar:hover {
            filter: brightness(1.1);
        }
        .chart-point {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .chart-point:hover {
            r: 6;
            stroke-width: 3;
        }
        @keyframes lineDraw {
            to { stroke-dashoffset: 0; }
        }
        .trend-line {
            stroke-dasharray: 500;
            stroke-dashoffset: 500;
            animation: lineDraw 1.2s ease-out forwards;
        }
        @keyframes pointFadeIn {
            from { opacity: 0; transform: scale(0); }
            to { opacity: 1; transform: scale(1); }
        }
        .chart-point {
            transform-origin: center;
            animation: pointFadeIn 0.3s ease-out forwards;
        }
        .export-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 8px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            min-width: 160px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: all 0.2s ease;
            z-index: 50;
        }
        .export-dropdown.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .export-dropdown a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            color: #374151;
            font-size: 14px;
            text-decoration: none;
            transition: background 0.15s;
        }
        .export-dropdown a:hover {
            background: #f3f4f6;
        }
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 100;
        }
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast.success { background: #10b981; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex overflow-hidden">

    <!-- SIDEBAR (Matches dashboard.blade.php exactly) -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between fixed h-full z-30">
        <div>
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">SupportDesk</h1>
                <p class="text-xs text-gray-400 mt-1">E-commerce Support</p>
            </div>
            <nav class="p-4 space-y-1">
                <a href="{{ route('admin.support.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ route('admin.support.tickets.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="ticket" class="w-5 h-5"></i> Tickets
                </a>
                <a href="{{ route('KnowledgeBase') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="book-open" class="w-5 h-5"></i> Knowledge Base
                </a>
                <a href="{{ route('sla-reports.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg transition-all duration-300">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-blue-600"></i> SLA Reports
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-gray-100">
            <a href="{{ route('CustomerPortal') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-purple-700 hover:bg-purple-50 rounded-lg transition-all duration-300">
                <i data-lucide="user" class="w-5 h-5"></i> Customer Portal
            </a>
        </div>
    </aside>

    <!-- RIGHT CONTENT AREA -->
    <div class="flex-1 pl-64 flex flex-col h-screen overflow-hidden relative">

        <!-- STICKY HEADER (Matches dashboard.blade.php exactly) -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 flex-shrink-0">
            <div class="relative w-96">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <form action="{{ route('admin.support.tickets.index') }}" method="GET">
                    <input type="text" name="search" placeholder="Search tickets, customers, articles..." class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                </form>
            </div>
            <div class="flex items-center gap-4">
                <div x-data="{ notificationsOpen: false }" class="relative">
                    <button @click="notificationsOpen = !notificationsOpen" class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-all focus:outline-none cursor-pointer">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @php
                            $notifyCount = \App\Models\Ticket::where('status', 'open')->count();
                        @endphp
                        @if($notifyCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </button>
                    <div x-show="notificationsOpen" @click.outside="notificationsOpen = false" x-transition class="absolute right-0 mt-2 w-[360px] bg-white border border-gray-200 rounded-xl shadow-2xl z-50 flex flex-col overflow-hidden" style="display: none;">
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

        <!-- MAIN CONTENT -->
        <main class="p-8 flex-1 overflow-y-auto h-[calc(100vh-4rem)] hide-scrollbar">
            <div class="space-y-6">

                @if(session('success'))
                    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-xs font-medium shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Page Header with Export -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-950 tracking-tight">SLA REPORTS</h2>
                        <p class="text-sm text-gray-500 mt-1">Track and monitor service level agreement performance across all tickets</p>
                    </div>
                    <div class="relative">
                        <button id="exportBtn" class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Export
                            <i data-lucide="chevron-down" class="w-3 h-3"></i>
                        </button>
                        <div id="exportDropdown" class="export-dropdown">
                            <a href="{{ route('sla-reports.export', ['format' => 'csv']) }}" onclick="showToast('Exporting CSV...', 'success')">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-green-600"></i>
                                Export as CSV
                            </a>
                            <a href="{{ route('sla-reports.export', ['format' => 'json']) }}" onclick="showToast('Exporting JSON...', 'success')">
                                <i data-lucide="file-json" class="w-4 h-4 text-blue-600"></i>
                                Export as JSON
                            </a>
                            <a href="#" onclick="showToast('Print preview opened', 'success'); window.print(); return false;">
                                <i data-lucide="printer" class="w-4 h-4 text-gray-600"></i>
                                Print Report
                            </a>
                        </div>
                    </div>
                </div>

                @php
                    $hasData = $weeklyCompliance->sum('ticket_count') > 0 || $slaTargets->sum('ticket_count') > 0 || \App\Models\Ticket::count() > 0;
                @endphp

                @if(!$hasData)
                <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center gap-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-500"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">No ticket data available</p>
                        <p class="text-xs text-yellow-600">Create some tickets to see SLA metrics and charts populate from the database.</p>
                    </div>
                </div>
                @endif

                <!-- Stats Cards Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    @php
                        $compliance = $metrics['overall_compliance'] ?? null;
                        $response = $metrics['avg_response_time'] ?? null;
                        $resolution = $metrics['avg_resolution_time'] ?? null;
                        $totalTickets = \App\Models\Ticket::count();
                        $resolvedTickets = \App\Models\Ticket::whereNotNull('resolved_at')->count();
                        $openTickets = \App\Models\Ticket::where('status', 'open')->count();
                        $overdueTickets = \App\Models\Ticket::where('status', '!=', 'closed')
                            ->where('status', '!=', 'resolved')
                            ->where('due_date', '<', now())
                            ->count();
                    @endphp

                    <div class="dash-card-hover bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Overall Compliance</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1">{{ $compliance->metric_value ?? '0' }}%</h3>
                            <p class="text-xs text-gray-400 mt-1">{{ $resolvedTickets }} resolved / {{ $totalTickets }} total</p>
                        </div>
                        <div class="h-12 w-12 bg-green-50 rounded-lg flex items-center justify-center text-green-500">
                            <i data-lucide="check-circle-2" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>

                    <div class="dash-card-hover bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Avg. Response Time</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1">{{ $response->metric_value ?? '0h' }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Target: &lt; 2h</p>
                        </div>
                        <div class="h-12 w-12 bg-yellow-50 rounded-lg flex items-center justify-center text-yellow-500">
                            <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                        </div>
                    </div>

                    <div class="dash-card-hover bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Avg. Resolution Time</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1">{{ $resolution->metric_value ?? '0h' }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Target: &lt; 8h</p>
                        </div>
                        <div class="h-12 w-12 bg-purple-50 rounded-lg flex items-center justify-center text-purple-500">
                            <i data-lucide="trending-up" class="w-6 h-6 text-purple-600"></i>
                        </div>
                    </div>

                    <div class="dash-card-hover bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">SLA Breaches</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1">{{ $overdueTickets }}</h3>
                            <p class="text-xs text-gray-400 mt-1">{{ $openTickets }} open tickets</p>
                        </div>
                        <div class="h-12 w-12 bg-red-50 rounded-lg flex items-center justify-center text-red-500">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <!-- Weekly SLA Compliance Trend -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 relative">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Weekly SLA Compliance Trend</h3>
                        <div class="relative h-48" id="trendChartContainer">
                            <svg viewBox="0 0 600 180" class="w-full h-full" id="trendChart">
                                <line x1="50" y1="20" x2="550" y2="20" stroke="#e5e7eb" stroke-width="1"/>
                                <line x1="50" y1="60" x2="550" y2="60" stroke="#e5e7eb" stroke-width="1"/>
                                <line x1="50" y1="100" x2="550" y2="100" stroke="#e5e7eb" stroke-width="1"/>
                                <line x1="50" y1="140" x2="550" y2="140" stroke="#e5e7eb" stroke-width="1"/>
                                <text x="35" y="25" text-anchor="end" font-size="10" fill="#9ca3af">100</text>
                                <text x="35" y="65" text-anchor="end" font-size="10" fill="#9ca3af">75</text>
                                <text x="35" y="105" text-anchor="end" font-size="10" fill="#9ca3af">50</text>
                                <text x="35" y="145" text-anchor="end" font-size="10" fill="#9ca3af">25</text>
                                <text x="35" y="165" text-anchor="end" font-size="10" fill="#9ca3af">0</text>

                                @foreach($weeklyCompliance as $index => $week)
                                <text x="{{ 100 + ($index * 110) }}" y="175" text-anchor="middle" font-size="10" fill="#9ca3af">{{ $week->week_name }}</text>
                                @endforeach

                                @php
                                    $compPoints = [];
                                    $tktPoints = [];
                                    $maxTickets = max($weeklyCompliance->pluck('ticket_count')->max(), 1);
                                    foreach($weeklyCompliance as $index => $week) {
                                        $x = 100 + ($index * 110);
                                        $compY = 140 - (($week->compliance_percentage / 100) * 120);
                                        $tktY = 140 - (($week->ticket_count / max($maxTickets, 1)) * 120);
                                        $compPoints[] = "$x,$compY";
                                        $tktPoints[] = "$x,$tktY";
                                    }
                                @endphp

                                <polyline class="trend-line" points="{{ implode(' ', $compPoints) }}" fill="none" stroke="#3b82f6" stroke-width="2"/>
                                @foreach($weeklyCompliance as $index => $week)
                                @php
                                    $x = 100 + ($index * 110);
                                    $compY = 140 - (($week->compliance_percentage / 100) * 120);
                                @endphp
                                <circle class="chart-point" cx="{{ $x }}" cy="{{ $compY }}" r="4" fill="white" stroke="#3b82f6" stroke-width="2"
                                    data-week="{{ $week->week_name }}" data-compliance="{{ $week->compliance_percentage }}" data-ticket="{{ $week->ticket_count }}" style="animation-delay: {{ 0.8 + ($index * 0.1) }}s"/>
                                @endforeach

                                <polyline class="trend-line" points="{{ implode(' ', $tktPoints) }}" fill="none" stroke="#a855f7" stroke-width="2" style="animation-delay: 0.3s"/>
                                @foreach($weeklyCompliance as $index => $week)
                                @php
                                    $x = 100 + ($index * 110);
                                    $tktY = 140 - (($week->ticket_count / max($maxTickets, 1)) * 120);
                                @endphp
                                <circle class="chart-point" cx="{{ $x }}" cy="{{ $tktY }}" r="4" fill="white" stroke="#a855f7" stroke-width="2"
                                    data-week="{{ $week->week_name }}" data-compliance="{{ $week->compliance_percentage }}" data-ticket="{{ $week->ticket_count }}" style="animation-delay: {{ 0.8 + ($index * 0.1) }}s"/>
                                @endforeach
                            </svg>
                            <div id="trendTooltip" class="chart-tooltip">
                                <div class="tooltip-content">
                                    <p class="font-semibold text-gray-900 text-sm mb-1" id="trendTooltipWeek">Week 1</p>
                                    <p class="text-xs text-blue-600">Compliance % : <span id="trendTooltipCompliance">0</span></p>
                                    <p class="text-xs text-purple-600">Ticket : <span id="trendTooltipTicket">0</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-center gap-6 mt-2">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-0.5 bg-blue-500"></span>
                                <span class="text-xs text-gray-600">Compliance</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-0.5 bg-purple-500"></span>
                                <span class="text-xs text-gray-600">Ticket Count</span>
                            </div>
                        </div>
                    </div>

                    <!-- Performance by Priority Level -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 relative">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Performance by Priority Level</h3>
                        <div class="relative h-48" id="priorityChartContainer">
                            <svg viewBox="0 0 600 150" class="w-full h-full" id="priorityChart">
                                <line x1="50" y1="20" x2="550" y2="20" stroke="#e5e7eb" stroke-width="1"/>
                                <line x1="50" y1="50" x2="550" y2="50" stroke="#e5e7eb" stroke-width="1"/>
                                <line x1="50" y1="80" x2="550" y2="80" stroke="#e5e7eb" stroke-width="1"/>
                                <line x1="50" y1="110" x2="550" y2="110" stroke="#e5e7eb" stroke-width="1"/>
                                <text x="35" y="25" text-anchor="end" font-size="10" fill="#9ca3af">100</text>
                                <text x="35" y="55" text-anchor="end" font-size="10" fill="#9ca3af">75</text>
                                <text x="35" y="85" text-anchor="end" font-size="10" fill="#9ca3af">50</text>
                                <text x="35" y="115" text-anchor="end" font-size="10" fill="#9ca3af">25</text>
                                <text x="35" y="135" text-anchor="end" font-size="10" fill="#9ca3af">0</text>

                                @foreach($priorityPerformance as $index => $priority)
                                @php
                                    $x = 90 + ($index * 120);
                                    $barColors = ['critical' => '#ef4444', 'high' => '#f97316', 'medium' => '#eab308', 'low' => '#22c55e'];
                                    $color = $barColors[strtolower($priority->priority_level)] ?? '#3b82f6';
                                @endphp
                                <rect class="priority-bar" id="bar-{{ strtolower($priority->priority_level) }}" x="{{ $x }}" y="130" width="60" height="0" fill="{{ $color }}" rx="2"
                                    data-priority="{{ $priority->priority_level }}" data-compliance="{{ $priority->compliance_percentage }}"/>
                                <text x="{{ $x + 30 }}" y="145" text-anchor="middle" font-size="10" fill="#9ca3af">{{ $priority->priority_level }}</text>
                                @endforeach
                            </svg>
                            <div id="priorityTooltip" class="chart-tooltip">
                                <div class="tooltip-content">
                                    <p class="font-semibold text-gray-900 text-sm mb-1" id="priorityTooltipLabel">Critical</p>
                                    <p class="text-xs text-blue-600">Compliance % : <span id="priorityTooltipValue">0</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLA Targets Table -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">SLA Targets by Priority</h3>
                        <p class="text-sm text-gray-500 mt-1">Response time targets and actual performance from ticket database</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3 text-left">Priority Level</th>
                                    <th class="px-6 py-3 text-left">Target Time</th>
                                    <th class="px-6 py-3 text-left">Actual Time</th>
                                    <th class="px-6 py-3 text-left">Compliance</th>
                                    <th class="px-6 py-3 text-left">Ticket Count</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($slaTargets as $target)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $target->badge_color }} {{ $target->badge_text_color }}">
                                            {{ $target->priority_level }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $target->target_time }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $target->actual_time }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-900">{{ $target->compliance_percentage }}%</span>
                                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                                <div class="{{ $target->progress_color }} h-2 rounded-full transition-all duration-500" style="width: {{ $target->compliance_percentage }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $target->ticket_count }} tickets</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center text-sm {{ $target->status === 'On Track' ? 'text-green-600' : ($target->status === 'At Risk' ? 'text-yellow-600' : 'text-red-600') }}">
                                            <span class="w-2 h-2 {{ $target->status === 'On Track' ? 'bg-green-500' : ($target->status === 'At Risk' ? 'bg-yellow-500' : 'bg-red-500') }} rounded-full mr-2"></span>
                                            {{ $target->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Links Row -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <a href="{{ route('admin.support.tickets.index') }}" class="dash-card-hover flex items-center space-x-4 p-4 border border-gray-100 hover:border-blue-200 hover:bg-blue-50/30 rounded-xl transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0 group-hover:bg-blue-100 transition-colors">
                                <i data-lucide="ticket" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">View All Tickets</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Manage and track all support tickets</p>
                            </div>
                        </a>

                        <a href="{{ route('KnowledgeBase') }}" class="dash-card-hover flex items-center space-x-4 p-4 border border-gray-100 hover:border-purple-200 hover:bg-purple-50/30 rounded-xl transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0 group-hover:bg-purple-100 transition-colors">
                                <i data-lucide="book-open" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Knowledge Base</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Browse help articles and guides</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.support.dashboard') }}" class="dash-card-hover flex items-center space-x-4 p-4 border border-gray-100 hover:border-green-200 hover:bg-green-50/30 rounded-xl transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 shrink-0 group-hover:bg-green-100 transition-colors">
                                <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Admin Dashboard</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Overview of support operations</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script>
        // Lucide Icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        // Export Dropdown
        const exportBtn = document.getElementById('exportBtn');
        const exportDropdown = document.getElementById('exportDropdown');
        exportBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            exportDropdown.classList.toggle('open');
        });
        document.addEventListener('click', () => exportDropdown.classList.remove('open'));
        exportDropdown.addEventListener('click', (e) => e.stopPropagation());

        // Toast
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type}`;
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        // Bar Animation
        function animateBar(barId, targetPercent, duration = 800, delay = 0) {
            const bar = document.getElementById(barId);
            if (!bar) return;
            const zeroY = 130;
            const maxHeight = 110;
            const targetHeight = (targetPercent / 100) * maxHeight;
            const targetY = zeroY - targetHeight;
            setTimeout(() => {
                const startTime = performance.now();
                function step(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const ease = 1 - Math.pow(1 - progress, 3);
                    const currentHeight = targetHeight * ease;
                    const currentY = zeroY - currentHeight;
                    bar.setAttribute('height', currentHeight);
                    bar.setAttribute('y', currentY);
                    if (progress < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }, delay);
        }

        document.addEventListener('DOMContentLoaded', () => {
            @foreach($priorityPerformance as $index => $priority)
            animateBar('bar-{{ strtolower($priority->priority_level) }}', {{ $priority->compliance_percentage }}, 800, {{ 200 + ($index * 200) }});
            @endforeach
        });

        // Trend Chart Tooltips
        const trendTooltip = document.getElementById('trendTooltip');
        const trendPoints = document.querySelectorAll('#trendChart .chart-point');
        const weekData = {
            @foreach($weeklyCompliance as $week)
            '{{ $week->week_name }}': { compliance: {{ $week->compliance_percentage }}, ticket: {{ $week->ticket_count }} },
            @endforeach
        };
        trendPoints.forEach(point => {
            point.addEventListener('mouseenter', () => {
                const week = point.getAttribute('data-week');
                const data = weekData[week];
                document.getElementById('trendTooltipWeek').textContent = week;
                document.getElementById('trendTooltipCompliance').textContent = data.compliance;
                document.getElementById('trendTooltipTicket').textContent = data.ticket;
                const rect = point.getBoundingClientRect();
                const containerRect = document.getElementById('trendChartContainer').getBoundingClientRect();
                trendTooltip.style.left = (rect.left - containerRect.left + rect.width/2 - 70) + 'px';
                trendTooltip.style.top = (rect.top - containerRect.top - 90) + 'px';
                trendTooltip.classList.add('visible');
            });
            point.addEventListener('mouseleave', () => trendTooltip.classList.remove('visible'));
        });

        // Priority Chart Tooltips
        const priorityTooltip = document.getElementById('priorityTooltip');
        const priorityBars = document.querySelectorAll('#priorityChart .priority-bar');
        const priorityData = {
            @foreach($priorityPerformance as $priority)
            '{{ $priority->priority_level }}': {{ $priority->compliance_percentage }},
            @endforeach
        };
        priorityBars.forEach(bar => {
            bar.addEventListener('mouseenter', () => {
                const priority = bar.getAttribute('data-priority');
                document.getElementById('priorityTooltipLabel').textContent = priority;
                document.getElementById('priorityTooltipValue').textContent = priorityData[priority];
                const rect = bar.getBoundingClientRect();
                const containerRect = document.getElementById('priorityChartContainer').getBoundingClientRect();
                priorityTooltip.style.left = (rect.left - containerRect.left + rect.width/2 - 70) + 'px';
                priorityTooltip.style.top = (rect.top - containerRect.top - 75) + 'px';
                priorityTooltip.classList.add('visible');
                bar.style.filter = 'brightness(1.2)';
            });
            bar.addEventListener('mouseleave', () => {
                priorityTooltip.classList.remove('visible');
                bar.style.filter = 'brightness(1)';
            });
        });

        // Table Row Hover Sync
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', () => {
                const badge = row.querySelector('span[class*="bg-"]');
                if (badge) {
                    const text = badge.textContent.trim();
                    priorityBars.forEach(bar => {
                        if (bar.getAttribute('data-priority') === text) bar.style.filter = 'brightness(1.2)';
                    });
                }
            });
            row.addEventListener('mouseleave', () => {
                priorityBars.forEach(bar => bar.style.filter = 'brightness(1)');
            });
        });
    </script>
</body>
</html>