<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupportDesk - Ticket Details</title>
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
        @keyframes detailFadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Staggered entrance applied to panels */
        .animate-detail-reveal {
            opacity: 0;
            animation: detailFadeInUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: calc(var(--panel-index, 0) * 60ms);
        }

        /* Micro-interactions for interactive details elements */
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

    <!-- FIXED SIDEBAR -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between fixed h-full z-30">
        <div>
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    SupportDesk
                </h1>
                <p class="text-xs text-gray-400 mt-1">E-commerce Support</p>
            </div>
            <nav class="p-4 space-y-1">
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg transition-all duration-300">
                    <i data-lucide="ticket" class="w-5 h-5 text-blue-600"></i> Tickets
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="book-open" class="w-5 h-5"></i> Knowledge Base
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i> SLA Reports
                </a>
            </nav>
        </div>

        <!-- Update this wrapper link at the bottom-left of details.blade.php -->
<div class="p-4 border-t border-gray-100">
    <a href="{{ route('customer') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-purple-700 hover:bg-purple-50 rounded-lg transition-all duration-300">
        <i data-lucide="user" class="w-5 h-5"></i>
        Customer Portal
    </a>
</div>
    </aside>

    <!-- RIGHT CONTENT AREA -->
    <div class="flex-1 pl-64 flex flex-col h-screen overflow-hidden">
        
        <!-- STICKY HEADER NAVBAR -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 flex-shrink-0">
            <div class="relative w-96">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" placeholder="Search tickets, customers, articles..." class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
            </div>
            <div class="flex items-center gap-4">
                <button class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-all">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <div class="flex items-center gap-3 pl-2 border-l border-gray-200">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold text-xs">AD</div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 leading-tight">Admin User</p>
                        <p class="text-[10px] text-gray-400">Support Manager</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- INDEPENDENT SCROLLABLE BODY CONTAINER -->
        <main class="p-8 flex-1 overflow-y-auto h-[calc(100vh-4rem)]">
            
            <button onclick="window.history.back()" class="flex items-center gap-2 text-gray-500 hover:text-gray-900 text-sm font-medium hover:-translate-x-1 transition-all duration-300 mb-6">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Tickets
            </button>

            <!-- Grid Layout split -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- LEFT PANELS: Content, Activity stream -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Ticket Main Block Card -->
                    <div class="animate-detail-reveal bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-4" style="--panel-index: 0;">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">TKT-1001</span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded uppercase bg-orange-50 text-orange-600">High</span>
                                <span class="text-xs font-semibold text-red-600 bg-red-50 flex items-center gap-1 px-2 py-0.5 rounded">
                                    <i data-lucide="alert-triangle" class="w-3 h-3"></i> Overdue
                                </span>
                            </div>
                            <select class="text-sm font-medium border border-gray-200 rounded-lg px-3 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                                <option value="open" selected>Open</option>
                                <option value="in-progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Order #54321 not received after 10 days</h3>
                            <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                                I placed an order 10 days ago and still haven't received it. The tracking shows it's stuck at the distribution center. Can you please help expedite this?
                            </p>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-md hover:bg-gray-200 transition-colors duration-200 cursor-default">
                                <i data-lucide="tag" class="w-3 h-3"></i> delivery-delay
                            </span>
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-md hover:bg-gray-200 transition-colors duration-200 cursor-default">
                                <i data-lucide="tag" class="w-3 h-3"></i> tracking-issue
                            </span>
                        </div>
                    </div>

                    <!-- Activity & Notes Card -->
                    <div class="animate-detail-reveal bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden" style="--panel-index: 1;">
                        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                            <h4 class="font-bold text-gray-900 text-base">Activity & Notes</h4>
                        </div>

                        <!-- Chat stream logs -->
                        <div class="p-6 space-y-6 flex flex-col gap-4">
                            @php
                                $conversations = DB::table('customer_conversations')
                                                    ->where('ticket_id', 1001)
                                                    ->orderBy('sent_at', 'asc')
                                                    ->get();
                            @endphp

                            @foreach($conversations as $msg)
                                @if($msg->sender == 'System')
                                    <!-- Automated System Bot Notification Bubble -->
                                    <div class="flex items-center justify-center my-1">
                                        <div class="bg-gray-100 text-gray-500 text-xs px-4 py-2 rounded-full border border-gray-200 flex items-center gap-2 shadow-sm italic">
                                            <i data-lucide="bot" class="w-3.5 h-3.5 text-gray-400"></i>
                                            {{ $msg->message }}
                                        </div>
                                    </div>
                                @else
                                    <!-- Regular Chat Bubble (Agent / Customer) -->
                                    <div class="flex gap-4 {{ $msg->sender == 'Agent' ? 'flex-row-reverse' : '' }}">
                                        <div class="w-9 h-9 {{ $msg->sender == 'Agent' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }} font-semibold rounded-full flex-shrink-0 flex items-center justify-center text-sm shadow-sm">
                                            {{ $msg->sender == 'Agent' ? 'AD' : 'CC' }}
                                        </div>
                                        <div class="space-y-1 flex-1 {{ $msg->sender == 'Agent' ? 'text-right' : '' }}">
                                            <div class="flex items-baseline gap-2 {{ $msg->sender == 'Agent' ? 'justify-end' : 'justify-between' }}">
                                                <h5 class="font-semibold text-sm text-gray-900">{{ $msg->sender == 'Agent' ? 'Admin User' : 'Charlize Casama' }}</h5>
                                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($msg->sent_at)->format('g:i A') }}</span>
                                            </div>
                                            <div class="inline-block p-4 rounded-xl text-sm leading-relaxed text-left shadow-sm {{ $msg->sender == 'Agent' ? 'bg-blue-600 text-white border border-blue-700' : 'bg-gray-50 text-gray-700 border border-gray-100' }}">
                                                {{ $msg->message }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Reply Input Form Block -->
                        <form action="{{ route('admin.support.tickets.reply', $ticket->id) }}" method="POST" class="...">
                            @csrf
                            <!-- Add this hidden input inside the agent form -->
    <input type="hidden" name="sender_type" value="Agent">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex-shrink-0 flex items-center justify-center font-semibold text-xs mt-1 shadow-sm">AD</div>
                                <textarea name="message" placeholder="Add a note or reply to customer...." rows="3" required class="w-full p-3 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none shadow-sm transition-all"></textarea>
                            </div>
                            <div class="flex items-center justify-between pl-11">
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-600 cursor-pointer select-none group">
                                    <input type="checkbox" class="rounded text-blue-600 border-gray-300 focus:ring-blue-500 w-4 h-4 transition-all">
                                    <span class="group-hover:text-gray-900 transition-colors">Internal note (note visible to customer)</span>
                                </label>
                                <button type="submit" class="flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow active:scale-[0.98] transition-all">
                                    Send <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- RIGHT SIDEBAR: Parameter Panels -->
                <div class="space-y-6">
                    <!-- Customer panel -->
                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4" style="--panel-index: 2;">
                        <h4 class="font-bold text-gray-900 text-sm">Customer</h4>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-sm font-semibold text-gray-600 shadow-sm">CC</div>
                            <div>
                                <p class="font-bold text-sm text-gray-900">Charlize Casama</p>
                                <p class="text-xs text-gray-400">charlizecasama@email.com</p>
                            </div>
                        </div>
                        <button class="w-full text-center py-2 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg active:scale-[0.98] transition-all">View full history</button>
                    </div>

                    <!-- Details assignment panel -->
                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4" style="--panel-index: 3;">
                        <h4 class="font-bold text-gray-900 text-sm">Ticket details</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 block mb-1">category</label>
                                <select class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                                    <option selected>Shipping & Delivery</option>
                                    <option>Wrong Item</option>
                                    <option>Promotions & Discounts</option>
                                    <option>Refunds</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 block mb-1">Assigned To</label>
                                <select class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                                    <option selected>Louise Lane</option>
                                    <option>Prinz Geon</option>
                                    <option>Emman Aragon</option>
                                    <option>Jerard Baluyot</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 block mb-1">Priority</label>
                                <select class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                                    <option>Critical</option>
                                    <option selected>High</option>
                                    <option>Medium</option>
                                    <option>Low</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SLA Panel -->
                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4" style="--panel-index: 4;">
                        <h4 class="font-bold text-gray-900 text-sm">SLA Status</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400 font-medium">SLA Deadline</span>
                                <span class="text-gray-800 font-bold">6/28/2026, 5:15:00 PM</span>
                            </div>
                            <div class="flex justify-between text-xs items-baseline">
                                <span class="text-gray-400 font-medium">Time remaining</span>
                                <span class="text-sm font-bold text-red-500 animate-pulse">39h overdue</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mt-1">
                                <div class="h-full bg-red-500 w-full rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Panel -->
                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-3" style="--panel-index: 5;">
                        <h4 class="font-bold text-gray-900 text-sm">Timeline</h4>
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                <span>Created: 6/26/2026, 9:15:00 AM</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                <span>Updated: 6/26/2026, 9:15:00 AM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Component Panel -->
                    <div class="animate-detail-reveal interactive-card bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-3" style="--panel-index: 6;">
                        <h4 class="font-bold text-gray-400 text-sm">Case Attachments (2)</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between border border-gray-100 rounded-lg p-2 bg-gray-50/50 hover:bg-gray-100/70 transition-colors duration-200 group">
                                <div class="flex items-center gap-2 text-xs">
                                    <i data-lucide="image" class="w-4 h-4 text-blue-500"></i>
                                    <div>
                                        <p class="font-medium text-gray-800">package-photo.png</p>
                                        <p class="text-[10px] text-gray-400">2.4 MB</p>
                                    </div>
                                </div>
                                <button class="p-1 text-gray-400 hover:text-gray-700 group-hover:scale-110 transition-transform"><i data-lucide="download" class="w-4 h-4"></i></button>
                            </div>
                            <div class="flex items-center justify-between border border-gray-100 rounded-lg p-2 bg-gray-50/50 hover:bg-gray-100/70 transition-colors duration-200 group">
                                <div class="flex items-center gap-2 text-xs">
                                    <i data-lucide="file-text" class="w-4 h-4 text-amber-500"></i>
                                    <div>
                                        <p class="font-medium text-gray-800">invoice-1234.pdf</p>
                                        <p class="text-[10px] text-gray-400">1.8 MB</p>
                                    </div>
                                </div>
                                <button class="p-1 text-gray-400 hover:text-gray-700 group-hover:scale-110 transition-transform"><i data-lucide="download" class="w-4 h-4"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>