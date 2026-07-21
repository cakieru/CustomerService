<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SupportDesk - SLA Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .sidebar-item { transition: all 0.2s; }
        .sidebar-item:hover { background-color: #f3f4f6; }
        .sidebar-item.active { background-color: #eff6ff; color: #2563eb; border-right: 3px solid #2563eb; }
        .progress-bar { transition: width 0.5s ease; }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }

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

        .tooltip-content {
            animation: tooltipFadeIn 0.2s ease;
        }
        @keyframes tooltipFadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
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
        .export-dropdown a:first-child {
            border-radius: 8px 8px 0 0;
        }
        .export-dropdown a:last-child {
            border-radius: 0 0 8px 8px;
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
        .toast.error { background: #ef4444; }

        /* Light violet for Customer Portal - DEFAULT STATE, not just hover */
        .customer-portal-link {
            color: #c4b5fd !important; /* violet-300 */
        }
        .customer-portal-link:hover {
            color: #a78bfa !important; /* violet-400 on hover */
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 min-h-screen fixed left-0 top-0">
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-gray-900">SupportDesk</h1>
                <p class="text-xs text-gray-500 mt-1">E-commerce Support</p>
            </div>
            <nav class="mt-4">
                <a href="#" class="sidebar-item flex items-center px-6 py-3 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-th-large w-6 text-gray-400"></i>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>
                <a href="#" class="sidebar-item flex items-center px-6 py-3 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-ticket-alt w-6 text-gray-400"></i>
                    <span class="text-sm font-medium">Tickets</span>
                </a>
                <a href="#" class="sidebar-item flex items-center px-6 py-3 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-book w-6 text-gray-400"></i>
                    <span class="text-sm font-medium">Knowledge Base</span>
                </a>
                <a href="#" class="sidebar-item active flex items-center px-6 py-3 text-blue-600">
                    <i class="fas fa-chart-bar w-6 text-blue-600"></i>
                    <span class="text-sm font-medium">SLA Reports</span>
                </a>
                <div class="mt-8 px-6">
                    <a href="#" class="flex items-center customer-portal-link">
                        <i class="fas fa-globe w-6 text-purple-400"></i>
                        <span class="text-sm font-medium">Customer Portal</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 flex-1">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
                <div class="flex-1 max-w-xl">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="Search tickets, customers, articles..." 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="relative p-2 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">AD</div>
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">Admin User</p>
                            <p class="text-xs text-gray-500">Support Manager</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-8">
                <!-- Page Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">SLA Reports</h2>
                        <p class="text-sm text-gray-500 mt-1">Track and monitor service level agreement performance</p>
                    </div>
                    <div class="relative">
                        <button id="exportBtn" class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-download text-xs"></i>
                            Export
                            <i class="fas fa-chevron-down text-xs ml-1"></i>
                        </button>
                        <div id="exportDropdown" class="export-dropdown">
                            <a href="{{ route('sla-reports.export', ['format' => 'csv']) }}" onclick="showToast('Exporting CSV...', 'success')">
                                <i class="fas fa-file-csv text-green-600"></i>
                                Export as CSV
                            </a>
                            <a href="{{ route('sla-reports.export', ['format' => 'json']) }}" onclick="showToast('Exporting JSON...', 'success')">
                                <i class="fas fa-file-code text-blue-600"></i>
                                Export as JSON
                            </a>
                            <a href="#" onclick="showToast('Print preview opened', 'success'); window.print(); return false;">
                                <i class="fas fa-print text-gray-600"></i>
                                Print Report
                            </a>
                        </div>
                    </div>
                </div>

                @php
                    // Check if we have real data from the database
                    $hasData = $weeklyCompliance->sum('ticket_count') > 0 || $slaTargets->sum('ticket_count') > 0;
                @endphp

                @if(!$hasData)
                <!-- No Data Alert -->
                <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">No ticket data available</p>
                        <p class="text-xs text-yellow-600">Create some tickets to see SLA metrics and charts.</p>
                    </div>
                </div>
                @endif

                <!-- Dashboard Grid -->
                <div class="grid grid-cols-12 gap-6">
                    <!-- Left Column - Metrics -->
                    <div class="col-span-3 space-y-6">
                        @php
                            $compliance = $metrics['overall_compliance'] ?? null;
                            $response = $metrics['avg_response_time'] ?? null;
                            $resolution = $metrics['avg_resolution_time'] ?? null;
                        @endphp

                        <!-- Overall Compliance -->
                        <div class="bg-white rounded-xl p-5 border border-gray-100 card-hover">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-500">{{ $compliance->metric_label ?? 'Overall Compliance' }}</span>
                                <div class="w-8 h-8 {{ $compliance->icon_bg ?? 'bg-green-100' }} rounded-full flex items-center justify-center">
                                    <i class="fas {{ $compliance->icon_class ?? 'fa-check' }} text-green-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">{{ $compliance->metric_value ?? '0' }}%</div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-green-500 h-2.5 rounded-full progress-bar" style="width: {{ $compliance->metric_value ?? '0' }}%"></div>
                            </div>
                        </div>

                        <!-- Avg Response Time -->
                        <div class="bg-white rounded-xl p-5 border border-gray-100 card-hover">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-500">{{ $response->metric_label ?? 'Avg. Response Time' }}</span>
                                <div class="w-8 h-8 {{ $response->icon_bg ?? 'bg-yellow-100' }} rounded-full flex items-center justify-center">
                                    <i class="fas {{ $response->icon_class ?? 'fa-clock' }} text-yellow-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">{{ $response->metric_value ?? '0 hours' }}</div>
                            @if($response && $response->trend_text)
                            <div class="flex items-center gap-1 text-green-600 text-sm">
                                <i class="fas fa-arrow-trend-{{ $response->trend_direction ?? 'up' }} text-xs"></i>
                                <span>{{ $response->trend_text }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Avg Resolution Time -->
                        <div class="bg-white rounded-xl p-5 border border-gray-100 card-hover">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-500">{{ $resolution->metric_label ?? 'Avg. Resolution Time' }}</span>
                                <div class="w-8 h-8 {{ $resolution->icon_bg ?? 'bg-purple-100' }} rounded-full flex items-center justify-center">
                                    <i class="fas {{ $resolution->icon_class ?? 'fa-chart-line' }} text-purple-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-900 mb-2">{{ $resolution->metric_value ?? '0 hours' }}</div>
                            @if($resolution && $resolution->target_value)
                            <p class="text-sm text-gray-500">{{ $resolution->target_value }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column - Charts -->
                    <div class="col-span-9 space-y-6">
                        <!-- Weekly SLA Compliance Trend -->
                        <div class="bg-white rounded-xl p-6 border border-gray-100 relative">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Weekly SLA Compliance Trend</h3>
                            <div class="relative h-48" id="trendChartContainer">
                                <svg viewBox="0 0 600 180" class="w-full h-full" id="trendChart">
                                    <!-- Grid Lines -->
                                    <line x1="50" y1="20" x2="550" y2="20" stroke="#e5e7eb" stroke-width="1"/>
                                    <line x1="50" y1="60" x2="550" y2="60" stroke="#e5e7eb" stroke-width="1"/>
                                    <line x1="50" y1="100" x2="550" y2="100" stroke="#e5e7eb" stroke-width="1"/>
                                    <line x1="50" y1="140" x2="550" y2="140" stroke="#e5e7eb" stroke-width="1"/>

                                    <!-- Y Axis Labels -->
                                    <text x="35" y="25" text-anchor="end" font-size="10" fill="#9ca3af">100</text>
                                    <text x="35" y="65" text-anchor="end" font-size="10" fill="#9ca3af">75</text>
                                    <text x="35" y="105" text-anchor="end" font-size="10" fill="#9ca3af">50</text>
                                    <text x="35" y="145" text-anchor="end" font-size="10" fill="#9ca3af">25</text>
                                    <text x="35" y="165" text-anchor="end" font-size="10" fill="#9ca3af">0</text>

                                    <!-- X Axis Labels -->
                                    @foreach($weeklyCompliance as $index => $week)
                                    <text x="{{ 100 + ($index * 120) }}" y="175" text-anchor="middle" font-size="10" fill="#9ca3af">{{ $week->week_name }}</text>
                                    @endforeach

                                    @php
                                        $compliancePoints = [];
                                        $ticketPoints = [];
                                        foreach($weeklyCompliance as $index => $week) {
                                            $x = 100 + ($index * 120);
                                            $compY = 140 - (($week->compliance_percentage / 100) * 120);
                                            $ticketY = 140 - (($week->ticket_count / 60) * 120);
                                            $compliancePoints[] = "$x,$compY";
                                            $ticketPoints[] = "$x,$ticketY";
                                        }
                                    @endphp

                                    <!-- Compliance Line (Blue) -->
                                    <polyline class="trend-line" points="{{ implode(' ', $compliancePoints) }}" 
                                              fill="none" stroke="#3b82f6" stroke-width="2"/>

                                    @foreach($weeklyCompliance as $index => $week)
                                    @php
                                        $x = 100 + ($index * 120);
                                        $compY = 140 - (($week->compliance_percentage / 100) * 120);
                                    @endphp
                                    <circle class="chart-point" cx="{{ $x }}" cy="{{ $compY }}" r="4" fill="white" stroke="#3b82f6" stroke-width="2"
                                            data-week="{{ $week->week_name }}" data-compliance="{{ $week->compliance_percentage }}" data-ticket="{{ $week->ticket_count }}" style="animation-delay: {{ 0.8 + ($index * 0.1) }}s"/>
                                    @endforeach

                                    <!-- Ticket Line (Purple) -->
                                    <polyline class="trend-line" points="{{ implode(' ', $ticketPoints) }}" 
                                              fill="none" stroke="#a855f7" stroke-width="2" style="animation-delay: 0.3s"/>

                                    @foreach($weeklyCompliance as $index => $week)
                                    @php
                                        $x = 100 + ($index * 120);
                                        $ticketY = 140 - (($week->ticket_count / 60) * 120);
                                    @endphp
                                    <circle class="chart-point" cx="{{ $x }}" cy="{{ $ticketY }}" r="4" fill="white" stroke="#a855f7" stroke-width="2"
                                            data-week="{{ $week->week_name }}" data-compliance="{{ $week->compliance_percentage }}" data-ticket="{{ $week->ticket_count }}" style="animation-delay: {{ 0.8 + ($index * 0.1) }}s"/>
                                    @endforeach
                                </svg>
                                <!-- Tooltip for Trend Chart -->
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
                                    <span class="text-xs text-gray-600">Ticket</span>
                                </div>
                            </div>
                        </div>

                        <!-- Performance by Priority Level -->
                        <div class="bg-white rounded-xl p-6 border border-gray-100 relative">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Performance by Priority Level</h3>
                            <div class="relative h-40" id="priorityChartContainer">
                                <svg viewBox="0 0 600 150" class="w-full h-full" id="priorityChart">
                                    <!-- Grid Lines -->
                                    <line x1="50" y1="20" x2="550" y2="20" stroke="#e5e7eb" stroke-width="1"/>
                                    <line x1="50" y1="50" x2="550" y2="50" stroke="#e5e7eb" stroke-width="1"/>
                                    <line x1="50" y1="80" x2="550" y2="80" stroke="#e5e7eb" stroke-width="1"/>
                                    <line x1="50" y1="110" x2="550" y2="110" stroke="#e5e7eb" stroke-width="1"/>

                                    <!-- Y Axis Labels -->
                                    <text x="35" y="25" text-anchor="end" font-size="10" fill="#9ca3af">100</text>
                                    <text x="35" y="55" text-anchor="end" font-size="10" fill="#9ca3af">75</text>
                                    <text x="35" y="85" text-anchor="end" font-size="10" fill="#9ca3af">50</text>
                                    <text x="35" y="115" text-anchor="end" font-size="10" fill="#9ca3af">25</text>
                                    <text x="35" y="135" text-anchor="end" font-size="10" fill="#9ca3af">0</text>

                                    @foreach($priorityPerformance as $index => $priority)
                                    @php
                                        $x = 90 + ($index * 130);
                                    @endphp
                                    <rect class="priority-bar" id="bar-{{ strtolower($priority->priority_level) }}" x="{{ $x }}" y="130" width="60" height="0" fill="#3b82f6" rx="2"
                                          data-priority="{{ $priority->priority_level }}" data-compliance="{{ $priority->compliance_percentage }}"/>

                                    <text x="{{ $x + 30 }}" y="145" text-anchor="middle" font-size="10" fill="#9ca3af">{{ $priority->priority_level }}</text>
                                    @endforeach
                                </svg>
                                <!-- Tooltip for Priority Chart -->
                                <div id="priorityTooltip" class="chart-tooltip">
                                    <div class="tooltip-content">
                                        <p class="font-semibold text-gray-900 text-sm mb-1" id="priorityTooltipLabel">Critical</p>
                                        <p class="text-xs text-blue-600">Compliance % : <span id="priorityTooltipValue">0</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLA Targets Table -->
                <div class="mt-6 bg-white rounded-xl border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">SLA Targets by Priority</h3>
                        <p class="text-sm text-gray-500 mt-1">Response time targets and actual performance</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compliance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
                                                <div class="{{ $target->progress_color }} h-2 rounded-full" style="width: {{ $target->compliance_percentage }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $target->ticket_count }} tickets</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center text-sm text-green-600">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            {{ $target->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script>
        // ============================================
        // EXPORT DROPDOWN
        // ============================================
        const exportBtn = document.getElementById('exportBtn');
        const exportDropdown = document.getElementById('exportDropdown');

        exportBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            exportDropdown.classList.toggle('open');
        });

        document.addEventListener('click', () => {
            exportDropdown.classList.remove('open');
        });

        exportDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // ============================================
        // TOAST NOTIFICATION
        // ============================================
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type}`;

            setTimeout(() => toast.classList.add('show'), 10);

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // ============================================
        // BAR ANIMATION UTILITY
        // ============================================
        function animateBar(barId, targetPercent, duration = 800, delay = 0) {
            const bar = document.getElementById(barId);
            if (!bar) return;

            const zeroY = 130;
            const maxHeight = 110;
            const targetHeight = (targetPercent / 100) * maxHeight;
            const targetY = zeroY - targetHeight;

            setTimeout(() => {
                const startTime = performance.now();
                const startHeight = 0;
                const startY = zeroY;

                function step(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const ease = 1 - Math.pow(1 - progress, 3);

                    const currentHeight = startHeight + (targetHeight - startHeight) * ease;
                    const currentY = startY - currentHeight;

                    bar.setAttribute('height', currentHeight);
                    bar.setAttribute('y', currentY);

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                }

                requestAnimationFrame(step);
            }, delay);
        }

        // ============================================
        // ANIMATE BARS ON PAGE LOAD
        // ============================================
        document.addEventListener('DOMContentLoaded', () => {
            @foreach($priorityPerformance as $index => $priority)
            animateBar('bar-{{ strtolower($priority->priority_level) }}', {{ $priority->compliance_percentage }}, 800, {{ 200 + ($index * 200) }});
            @endforeach
        });

        // ============================================
        // WEEKLY SLA COMPLIANCE TREND TOOLTIPS
        // ============================================
        const trendTooltip = document.getElementById('trendTooltip');
        const trendTooltipWeek = document.getElementById('trendTooltipWeek');
        const trendTooltipCompliance = document.getElementById('trendTooltipCompliance');
        const trendTooltipTicket = document.getElementById('trendTooltipTicket');
        const trendChartContainer = document.getElementById('trendChartContainer');
        const trendPoints = document.querySelectorAll('#trendChart .chart-point');

        const weekData = {
            @foreach($weeklyCompliance as $week)
            '{{ $week->week_name }}': { compliance: {{ $week->compliance_percentage }}, ticket: {{ $week->ticket_count }} },
            @endforeach
        };

        trendPoints.forEach(point => {
            point.addEventListener('mouseenter', (e) => {
                const week = point.getAttribute('data-week');
                const data = weekData[week];

                trendTooltipWeek.textContent = week;
                trendTooltipCompliance.textContent = data.compliance;
                trendTooltipTicket.textContent = data.ticket;

                const rect = point.getBoundingClientRect();
                const containerRect = trendChartContainer.getBoundingClientRect();
                const x = rect.left - containerRect.left + (rect.width / 2);
                const y = rect.top - containerRect.top;

                trendTooltip.style.left = (x - 70) + 'px';
                trendTooltip.style.top = (y - 90) + 'px';
                trendTooltip.classList.add('visible');

                point.style.r = '6';
                point.style.strokeWidth = '3';
            });

            point.addEventListener('mouseleave', () => {
                trendTooltip.classList.remove('visible');
                point.style.r = '4';
                point.style.strokeWidth = '2';
            });
        });

        // ============================================
        // PERFORMANCE BY PRIORITY LEVEL TOOLTIPS
        // ============================================
        const priorityTooltip = document.getElementById('priorityTooltip');
        const priorityTooltipLabel = document.getElementById('priorityTooltipLabel');
        const priorityTooltipValue = document.getElementById('priorityTooltipValue');
        const priorityChartContainer = document.getElementById('priorityChartContainer');
        const priorityBars = document.querySelectorAll('#priorityChart .priority-bar');

        const priorityData = {
            @foreach($priorityPerformance as $priority)
            '{{ $priority->priority_level }}': {{ $priority->compliance_percentage }},
            @endforeach
        };

        priorityBars.forEach(bar => {
            bar.addEventListener('mouseenter', (e) => {
                const priority = bar.getAttribute('data-priority');
                const compliance = priorityData[priority];

                priorityTooltipLabel.textContent = priority;
                priorityTooltipValue.textContent = compliance;

                const rect = bar.getBoundingClientRect();
                const containerRect = priorityChartContainer.getBoundingClientRect();
                const x = rect.left - containerRect.left + (rect.width / 2);
                const y = rect.top - containerRect.top;

                priorityTooltip.style.left = (x - 70) + 'px';
                priorityTooltip.style.top = (y - 75) + 'px';
                priorityTooltip.classList.add('visible');

                bar.style.filter = 'brightness(1.2)';
                bar.style.transform = 'scaleX(1.05)';
                bar.style.transformOrigin = 'center';
            });

            bar.addEventListener('mouseleave', () => {
                priorityTooltip.classList.remove('visible');
                bar.style.filter = 'brightness(1)';
                bar.style.transform = 'scaleX(1)';
            });
        });

        // ============================================
        // TABLE ROW HOVER SYNC
        // ============================================
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', () => {
                const priorityBadge = row.querySelector('span[class*="bg-"]');
                if (priorityBadge) {
                    const priorityText = priorityBadge.textContent.trim();
                    priorityBars.forEach(bar => {
                        if (bar.getAttribute('data-priority') === priorityText) {
                            bar.style.filter = 'brightness(1.2)';
                        }
                    });
                }
            });

            row.addEventListener('mouseleave', () => {
                priorityBars.forEach(bar => {
                    bar.style.filter = 'brightness(1)';
                });
            });
        });
    </script>
</body>
</html>