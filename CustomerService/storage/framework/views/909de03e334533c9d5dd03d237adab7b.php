<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupportDesk Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Hide scrollbar for cleaner look but keep functionality */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800 h-screen overflow-hidden flex">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between flex-shrink-0 h-full">
        <div>
            <div class="px-6 pt-8 pb-6">
                <h1 class="text-xl font-bold text-gray-900">SupportDesk</h1>
                <p class="text-sm text-gray-500 mt-1">E-commerce Support</p>
            </div>

            <nav class="px-4 space-y-1">
                <a href="<?php echo e(route('admin.support.dashboard')); ?>" class="flex items-center space-x-3 px-3 py-2.5 bg-blue-50 text-blue-700 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="<?php echo e(route('agent')); ?>" class="flex items-center space-x-3 px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    <span>Tickets</span>
                </a>

                <a href="<?php echo e(route('KnowledgeBase')); ?>" class="flex items-center space-x-3 px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 662 6l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                    </svg>
                    <span>Knowledge Base</span>
                </a>

                <a href="<?php echo e(route('admin.support.reports')); ?>" class="flex items-center space-x-3 px-3 py-2.5 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>SLA Reports</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-gray-200">
            <a href="<?php echo e(route('CustomerPortal')); ?>" class="flex items-center space-x-3 px-3 py-2.5 text-blue-600 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Customer Portal</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-full bg-white relative">
        
        <!-- Header Section -->
        <header class="h-16 border-b border-gray-200 bg-white flex items-center justify-between px-8 flex-shrink-0">
            <!-- Left Side: Search Bar -->
            <div class="relative w-96">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <form action="<?php echo e(route('admin.support.tickets.index')); ?>" method="GET">
                    <input type="text" name="search" placeholder="Search tickets by reference or subject..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </form>
            </div>

            <!-- Right Side Group: Notification Bell + Admin Profile -->
            <div class="flex items-center space-x-6">
                <?php
                    // Dynamically pull the 5 newest tickets straight from the DB in real-time!
                    $notifications = \App\Models\Ticket::latest()->take(5)->get();
                ?>
                            
                <div x-data="{ notificationsOpen: false }" class="relative">
                    <button @click="notificationsOpen = !notificationsOpen" class="relative text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <?php if(count($notifications) > 0): ?>
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                        <?php endif; ?>
                    </button>

                    <div x-show="notificationsOpen" @click.outside="notificationsOpen = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50" style="display: none;">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <h3 class="font-semibold text-gray-800 text-sm">Real-time Unread Tickets</h3>
                            <span class="text-xs bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded-full"><?php echo e(count($notifications)); ?></span>
                        </div>
                        <div class="max-h-96 overflow-y-auto divide-y divide-gray-50">
                            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notify): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <a href="<?php echo e(route('admin.support.tickets.show', $notify->id)); ?>" class="flex px-4 py-3 hover:bg-gray-50 transition">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mr-3">
                                        <?php echo e(substr($notify->customer->name ?? $notify->customer_name ?? 'C', 0, 1)); ?>

                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-semibold text-gray-900"><?php echo e($notify->subject ?? 'New Support Ticket'); ?></p>
                                        <p class="text-[11px] text-gray-500 truncate max-w-[180px]">
                                            Ref: <span class="text-blue-600 font-medium"><?php echo e($notify->ticket_reference ?? $notify->reference_code ?? 'TKT-'.$notify->id); ?></span> by <?php echo e($notify->customer->name ?? $notify->customer_name ?? 'Guest'); ?>

                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-0.5"><?php echo e($notify->created_at->diffForHumans()); ?></p>
                                    </div>
                                    <div class="w-2 h-2 rounded-full bg-blue-600 mt-1"></div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="p-6 text-center text-xs text-gray-400">All caught up! No unread notifications.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <button class="flex items-center space-x-3 text-left focus:outline-none">
                    <div class="h-9 w-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm">AD</div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 leading-tight">Admin User</p>
                        <p class="text-xs text-gray-500">Support Manager</p>
                    </div>
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto hide-scrollbar p-8 bg-gray-50/50">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <?php if(session('success')): ?>
                    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-xs font-medium shadow-sm">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
                
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">DASHBOARD</h2>
                    <p class="text-sm text-gray-500 mt-1">Overview of support operations and dynamic real-time live feed indicators</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Open Tickets</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1"><?php echo e($summary['openTickets']); ?></h3>
                        </div>
                        <div class="h-12 w-12 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">In Progress</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1"><?php echo e($summary['inProgress']); ?></h3>
                        </div>
                        <div class="h-12 w-12 bg-yellow-50 rounded-lg flex items-center justify-center text-yellow-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Resolved Today</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1"><?php echo e($summary['resolvedToday']); ?></h3>
                        </div>
                        <div class="h-12 w-12 bg-green-50 rounded-lg flex items-center justify-center text-green-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Critical Priority</p>
                            <h3 class="text-4xl font-bold text-gray-900 mt-1"><?php echo e($summary['criticalPriority']); ?></h3>
                        </div>
                        <div class="h-12 w-12 bg-red-50 rounded-lg flex items-center justify-center text-red-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900">Recent Tickets</h3>
                            <a href="<?php echo e(route('admin.support.tickets.index')); ?>" class="text-sm font-semibold text-blue-600 hover:underline">View all</a>
                        </div>
                        <div class="divide-y divide-gray-100 flex-1">
                            <?php $__empty_1 = true; $__currentLoopData = $recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <a href="<?php echo e(route('admin.support.tickets.show', $ticket->id)); ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="space-y-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-bold text-gray-900"><?php echo e($ticket->ticket_reference); ?></span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold capitalize 
                                                    <?php echo e($ticket->priority === 'critical' || $ticket->priority === 'high' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-gray-100 text-gray-700'); ?>">
                                                    <?php echo e($ticket->priority); ?>

                                                </span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-900 truncate max-w-sm"><?php echo e($ticket->subject); ?></p>
                                            <p class="text-xs text-gray-500">Client: <?php echo e($ticket->customer->name ?? 'Guest Client'); ?></p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded text-xs font-semibold capitalize 
                                            <?php echo e($ticket->status === 'open' ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-600'); ?>">
                                            <?php echo e($ticket->status); ?>

                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="p-8 text-center text-sm text-gray-400">No active recent support tickets found.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900">SLA Alerts</h3>
                            <span class="text-xs bg-red-100 text-red-800 font-bold px-2 py-1 rounded-full"><?php echo e(count($slaAlerts)); ?> Breach Overdue</span>
                        </div>
                        <div class="divide-y divide-gray-100 flex-1">
                            <?php $__empty_1 = true; $__currentLoopData = $slaAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <a href="<?php echo e(route('admin.support.tickets.show', $alert->id)); ?>" class="flex px-6 py-4 space-x-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0 mt-0.5 text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900 truncate max-w-xs"><?php echo e($alert->subject); ?></p>
                                        <p class="text-xs text-gray-500 mt-0.5">Ref: <span class="font-semibold text-gray-700"><?php echo e($alert->ticket_reference); ?></span> • Resolution Deadline Breached</p>
                                        <p class="text-xs text-gray-400 mt-1">Assigned Agent: <span class="text-gray-600 font-medium"><?php echo e($alert->agent->name ?? 'Unassigned Tier 1'); ?></span></p>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="p-8 text-center text-sm text-gray-400 flex flex-col items-center justify-center h-full">
                                    <svg class="w-8 h-8 text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="font-medium text-gray-600">Great job! No pending SLA breaches.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <a href="/tickets" class="flex items-center space-x-4 p-4 border border-gray-100 hover:border-blue-200 hover:bg-blue-50/30 rounded-xl transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0 group-hover:bg-blue-100 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Create New Ticket</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Manual ticket entry</p>
                            </div>
                        </a>

                        <a href="/knowledge-base" class="flex items-center space-x-4 p-4 border border-gray-100 hover:border-purple-200 hover:bg-purple-50/30 rounded-xl transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0 group-hover:bg-purple-100 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Browse help articles</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Knowledge base</p>
                            </div>
                        </a>

                        <a href="/sla-reports" class="flex items-center space-x-4 p-4 border border-gray-100 hover:border-green-200 hover:bg-green-50/30 rounded-xl transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 shrink-0 group-hover:bg-green-100 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
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

</body>
</html><?php /**PATH C:\Users\Administrator\Documents\GitHub\CustomerService\CustomerService\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>