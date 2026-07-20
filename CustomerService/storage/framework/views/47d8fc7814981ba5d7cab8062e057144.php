<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center - My Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js (Required for your tab switching) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen text-gray-800">

    <nav class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                🎧
            </div>
            <div>
                <h1 class="font-bold text-lg leading-tight">Support Center</h1>
                <p class="text-xs text-gray-500">We're here to help</p>
            </div>
        </div>
        <div class="flex items-center space-x-6">
            <a href="<?php echo e(route('CustomerPortal')); ?>" class="font-medium text-gray-500 hover:text-gray-900">Home</a>
            <a href="<?php echo e(route('customer.tickets')); ?>" class="font-medium text-blue-600 bg-blue-50 px-4 py-2 rounded-lg">My Tickets</a>
            <a href="<?php echo e(route('customer.create')); ?>" class="bg-blue-600 text-white font-medium px-5 py-2.5 rounded-lg hover:bg-blue-700 transition shadow-sm">+ New Request</a>
        </div>
    </nav>

    <main class="flex-grow flex flex-col justify-start py-12 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto w-full">
        
        <!-- We initialize Alpine.js here to handle dynamic tab switching seamlessly -->
        <div class="w-full" x-data="{ activeTab: 'all' }">
            <!-- Breadcrumb -->
            <div class="text-xs text-gray-400 mb-4 font-medium">
                <a href="<?php echo e(route('CustomerPortal')); ?>" class="hover:text-gray-600">Home</a> &gt; <span class="text-gray-600">My Tickets</span>
            </div>

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-1">My Support Tickets</h1>
                <p class="text-sm text-gray-500">Track the status of your open and past requests</p>
            </div>

            <!-- Dynamic Status Counter Cards -->
            <?php
            $totalTickets = count($tickets);
            $openCount = $tickets->where('status', 'open')->count();
            $inProgressCount = $tickets->whereIn('status', ['in progress', 'in_progress', 'processing', 'in-progress'])->count();
            $resolvedCount = $tickets->where('status', 'resolved')->count();
            $closedCount = $tickets->where('status', 'closed')->count();
            ?>

            <!-- Clicking these counter cards will now change the tab list below too! -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div @click="activeTab = 'open'" class="bg-[#FFFDF5] border border-[#FDE68A] rounded-2xl p-6 shadow-sm flex flex-col justify-center cursor-pointer hover:scale-[1.02] transition-transform">
                    <span class="text-3xl font-bold text-[#D97706] mb-1"><?php echo e($openCount); ?></span>
                    <span class="text-xs font-medium text-gray-500">open</span>
                </div>
                <div @click="activeTab = 'in-progress'" class="bg-[#F0F9FF] border border-[#BAE6FD] rounded-2xl p-6 shadow-sm flex flex-col justify-center cursor-pointer hover:scale-[1.02] transition-transform">
                    <span class="text-3xl font-bold text-[#0284C7] mb-1"><?php echo e($inProgressCount); ?></span>
                    <span class="text-xs font-medium text-gray-500">in process</span>
                </div>
                <div @click="activeTab = 'resolved'" class="bg-[#F0FDF4] border border-[#BBF7D0] rounded-2xl p-6 shadow-sm flex flex-col justify-center cursor-pointer hover:scale-[1.02] transition-transform">
                    <span class="text-3xl font-bold text-[#16A34A] mb-1"><?php echo e($resolvedCount); ?></span>
                    <span class="text-xs font-medium text-gray-500">resolved</span>
                </div>
                <div @click="activeTab = 'closed'" class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-6 shadow-sm flex flex-col justify-center cursor-pointer hover:scale-[1.02] transition-transform">
                    <span class="text-3xl font-bold text-[#64748B] mb-1"><?php echo e($closedCount); ?></span>
                    <span class="text-xs font-medium text-gray-500">closed</span>
                </div>
            </div>

            <!-- Functional Search & Filter Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <!-- Working Search Form -->
                <form method="GET" action="<?php echo e(route('customer.tickets')); ?>" class="relative flex-grow max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search by subject or ticket ID..." class="w-full pl-10 pr-4 py-2.5 text-xs bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent shadow-sm">
                </form>

                <!-- Dynamic Dynamic Toggles -->
                <div class="flex items-center space-x-1.5 overflow-x-auto pb-2 md:pb-0 text-xs font-medium">
                    <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-gray-100 text-gray-800' : 'text-gray-500 hover:bg-gray-50'" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition cursor-pointer">
                        All (<?php echo e($totalTickets); ?>)
                    </button>
                    <button @click="activeTab = 'open'" :class="activeTab === 'open' ? 'bg-gray-100 text-gray-800' : 'text-gray-500 hover:bg-gray-50'" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition cursor-pointer">
                        Open (<?php echo e($openCount); ?>)
                    </button>
                    <button @click="activeTab = 'in-progress'" :class="activeTab === 'in-progress' ? 'bg-gray-100 text-gray-800' : 'text-gray-500 hover:bg-gray-50'" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition cursor-pointer">
                        In progress (<?php echo e($inProgressCount); ?>)
                    </button>
                    <button @click="activeTab = 'resolved'" :class="activeTab === 'resolved' ? 'bg-gray-100 text-gray-800' : 'text-gray-500 hover:bg-gray-50'" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition cursor-pointer">
                        Resolved (<?php echo e($resolvedCount); ?>)
                    </button>
                    <button @click="activeTab = 'closed'" :class="activeTab === 'closed' ? 'bg-gray-100 text-gray-800' : 'text-gray-500 hover:bg-gray-50'" class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition cursor-pointer">
                        Closed (<?php echo e($closedCount); ?>)
                    </button>
                </div>
            </div>

            <!-- Dynamic Ticket List Rows -->
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $status = strtolower($ticket->status ?? 'open');
                        
                        $alpineStatus = 'open';
                        $badgeStyle = 'bg-[#FFFBEB] text-[#D97706] border-[#FDE68A]'; 
                        
                        if (in_array($status, ['in progress', 'in_progress', 'processing', 'in-progress'])) {
                            $alpineStatus = 'in-progress';
                            $badgeStyle = 'bg-[#EFF6FF] text-[#2563EB] border-[#BFDBFE]';
                        } elseif ($status == 'resolved') {
                            $alpineStatus = 'resolved';
                            $badgeStyle = 'bg-[#ECFDF5] text-[#059669] border-[#A7F3D0]';
                        } elseif ($status == 'closed') {
                            $alpineStatus = 'closed';
                            $badgeStyle = 'bg-gray-100 text-gray-600 border-gray-200';
                        }
                    ?>

                    <!-- The x-show handler filters rows based on your active category tab selection -->
                    <a x-show="activeTab === 'all' || activeTab === '<?php echo e($alpineStatus); ?>'" 
                       href="<?php echo e(url('/tickets/' . $ticket->id)); ?>" 
                       class="block bg-white border border-gray-200 rounded-2xl p-4 shadow-sm hover:border-gray-300 transition flex items-center justify-between group">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gray-100 text-gray-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <div class="flex items-center space-x-2.5 mb-1">
                                    <span class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition"><?php echo e($ticket->subject ?? $ticket->title ?? 'Support Request #' . $ticket->id); ?></span>
                                    <span class="px-2.5 py-0.5 border rounded-full text-[10px] font-bold capitalize <?php echo e($badgeStyle); ?>"><?php echo e($ticket->status ?? 'Open'); ?></span>
                                </div>
                                <div class="text-[11px] text-gray-400 font-medium flex items-center space-x-2">
                                    <span>TKT-<?php echo e($ticket->id); ?></span>
                                    <span>•</span>
                                    <span><?php echo e($ticket->category ?? 'General Support'); ?></span>
                                    <span>•</span>
                                    <span>Updated <?php echo e($ticket->updated_at ? $ticket->updated_at->diffForHumans() : 'Recently'); ?></span>
                                    <?php if(isset($ticket->agent)): ?>
                                        <span>•</span>
                                        <span>Agent: <?php echo e($ticket->agent->name); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-gray-400 group-hover:translate-x-1 transition-transform pr-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center shadow-sm">
                        <p class="text-sm font-bold text-gray-600 mb-1">No support tickets found</p>
                        <p class="text-xs text-gray-400">When you create a new request, it will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination Support -->
            <?php if(method_exists($tickets, 'links')): ?>
                <div class="mt-6">
                    <?php echo e($tickets->links()); ?>

                </div>
            <?php endif; ?>
        </div>

    </main>

    <footer class="bg-white border-t border-gray-200 py-6 px-8 flex justify-between items-center text-sm text-gray-500 mt-auto">
        <p>&copy; 2026 Support Center. All rights reserved.</p>
        <div class="flex space-x-6">
            <a href="#" class="hover:text-gray-900">Agent Portal</a>
            <a href="#" class="hover:text-gray-900">FAQ</a>
            <a href="#" class="hover:text-gray-900">Terms</a>
        </div>
    </footer>

</body>
</html><?php /**PATH C:\Users\Administrator\Documents\GitHub\CustomerService\CustomerService\resources\views/customer/index.blade.php ENDPATH**/ ?>