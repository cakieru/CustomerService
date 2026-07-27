<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $ticket->id }} - Support Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:wght@100..1000&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
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
    <style>
        @keyframes dynamicRevealUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-reveal-card {
            opacity: 0;
            animation: dynamicRevealUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: calc(var(--card-index, 0) * 65ms);
        }
        .scrolly-view::-webkit-scrollbar { width: 5px; }
        .scrolly-view::-webkit-scrollbar-track { background: transparent; }
        .scrolly-view::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
    </style>
</head>
<body class="bg-[#fcfdfe] text-gray-700 font-sans antialiased h-full flex flex-col overflow-hidden">

    <!-- HEADER -->
    <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ route('CustomerPortal') }}" class="flex items-center space-x-3 group cursor-pointer select-none">
                <div class="bg-[#0f4c81] text-white p-2.5 rounded-xl shadow-sm group-hover:scale-105 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold text-gray-800 leading-tight">Support Center</h1>
                    <p class="text-xs text-gray-400 font-medium">We're here to help</p>
                </div>
            </a>
            <nav class="flex items-center space-x-8">
                <a href="{{ route('CustomerPortal') }}" class="text-gray-500 hover:text-gray-800 font-medium text-sm transition-colors">Home</a>
                <a href="{{ route('customer.tickets') }}" class="bg-[#f0f4f8] text-[#0f4c81] font-semibold px-4 py-2 rounded-xl text-sm hover:opacity-90">My Tickets</a> 
                <a href="{{ route('customer.create') }}" class="bg-[#0f62fe] hover:bg-[#0052cc] text-white font-semibold px-5 py-2.5 rounded-xl text-sm shadow-sm hover:shadow-md flex items-center space-x-1.5 transition-all hover:-translate-y-0.5 active:translate-y-0">
                    <span>+ New Request</span>
                </a>
            </nav>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto scrolly-view flex flex-col justify-between">
        <main class="max-w-6xl w-full mx-auto px-8 py-8 flex-grow">
            
            <!-- Breadcrumbs -->
            <div class="animate-reveal-card flex items-center gap-2 text-xs font-medium text-gray-400 mb-8" style="--card-index: 0;">
                <a href="{{ route('CustomerPortal') }}" class="hover:text-gray-600 transition">Home</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <a href="{{ route('customer.tickets') }}" class="hover:text-gray-600 transition">My Tickets</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span class="text-gray-500 font-semibold">Ticket #{{ $ticket->id }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- LEFT COLUMN -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Ticket Header -->
                    @php
                        $status = strtolower($ticket->status ?? 'open');
                        $statusColors = [
                            'open' => 'border-amber-400/80 text-amber-500',
                            'in progress' => 'border-blue-400/80 text-blue-500',
                            'in_progress' => 'border-blue-400/80 text-blue-500',
                            'processing' => 'border-blue-400/80 text-blue-500',
                            'resolved' => 'border-green-400/80 text-green-500',
                            'closed' => 'border-gray-300 text-gray-500',
                        ];
                        $statusClass = $statusColors[$status] ?? $statusColors['open'];
                    @endphp

                    <div class="animate-reveal-card bg-white border border-gray-200/90 rounded-3xl p-8 shadow-[0_2px_12px_rgba(0,0,0,0.01)] space-y-5" style="--card-index: 1;">
                        <div class="flex items-center gap-3 text-xs text-gray-400 font-medium">
                            <span class="font-bold text-gray-900 bg-gray-100 px-2.5 py-1 rounded-lg">TKT-{{ $ticket->id }}</span>
                            <span>&bull;</span>
                            <span>{{ $ticket->category ?? 'General Support' }}</span>
                            <span>&bull;</span>
                            <span>Opened {{ $ticket->created_at ? $ticket->created_at->format('F d, Y, g:i A') : 'Recently' }}</span>
                        </div>
                        
                        <h2 class="text-2xl font-extrabold text-gray-950 tracking-tight leading-tight">{{ $ticket->subject ?? $ticket->title ?? 'Support Request #' . $ticket->id }}</h2>
                        
                        <div class="flex flex-wrap items-center gap-4 pt-1">
                            <span class="px-5 py-1 border {{ $statusClass }} bg-white rounded-full text-sm font-bold shadow-sm tracking-wide capitalize">
                                {{ $ticket->status ?? 'Open' }}
                            </span>
                            
                            @if($ticket->priority)
                            <div class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                                <span class="w-2 h-2 rounded-full {{ $ticket->priority === 'High' ? 'bg-red-500' : ($ticket->priority === 'Medium' ? 'bg-orange-500' : 'bg-green-500') }}"></span> 
                                {{ $ticket->priority }} Priority
                            </div>
                            @endif

                            @if($agent)
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-500">
                                <div class="w-5 h-5 bg-blue-100 text-blue-700 font-extrabold text-[9px] rounded-full flex items-center justify-center">
                                    {{ collect(explode(' ', $agent->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->join('') }}
                                </div>
                                Assigned to {{ $agent->name }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Conversation Thread Box -->
                    <div class="animate-reveal-card bg-white border border-gray-200/90 rounded-3xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.01)] space-y-6" style="--card-index: 2;">
                        
                        <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Conversation</h3>

                        {{-- 1. Initial Ticket Request Message --}}
                        <div class="flex gap-4">
                            <div class="w-9 h-9 bg-blue-100 text-[#0f62fe] font-bold text-xs rounded-full flex items-center justify-center shadow-inner flex-shrink-0">
                                {{ collect(explode(' ', auth()->user()->name ?? $ticket->customer_name ?? 'You'))->map(fn($n) => strtoupper(substr($n, 0, 1)))->join('') }}
                            </div>
                            <div class="space-y-1 flex-1 max-w-[80%]">
                                <div class="flex items-baseline gap-2 justify-start">
                                    <h5 class="font-semibold text-sm text-gray-900">{{ auth()->user()->name ?? $ticket->customer_name ?? 'You' }}</h5>
                                    <span class="text-xs text-gray-400">— You</span>
                                    <span class="text-xs text-gray-400">{{ $ticket->created_at ? $ticket->created_at->format('M d, Y, g:i A') : '' }}</span>
                                </div>
                                <div class="inline-block p-3 px-4 rounded-2xl text-sm leading-relaxed text-left bg-gray-100 text-gray-800 border border-gray-100">
                                    {{ $ticket->description ?? $ticket->message ?? 'No description provided.' }}
                                </div>
                            </div>
                        </div>

                        {{-- 2. Replies Loop --}}
                        @forelse($replies as $index => $msg)
                            
                            {{-- System Bot Message --}}
                            @if($msg->sender == 'System' || str_contains($msg->message, 'Thank you for reaching out!'))
                                <div class="flex justify-center my-4">
                                    <div class="flex items-center gap-2 bg-gray-100/80 border border-gray-200/60 text-gray-500 text-xs px-4 py-2 rounded-full shadow-sm">
                                        <i data-lucide="bot" class="w-3.5 h-3.5 text-gray-400"></i>
                                        <span>{{ $msg->message }}</span>
                                    </div>
                                </div>

                            {{-- Customer Message --}}
                            @elseif($msg->sender == 'Customer')
                                <div class="flex gap-4">
                                    <div class="w-9 h-9 bg-blue-100 text-[#0f62fe] font-bold text-xs rounded-full flex items-center justify-center shadow-inner flex-shrink-0">
                                        {{ collect(explode(' ', $msg->user_name ?? 'You'))->map(fn($n) => strtoupper(substr($n, 0, 1)))->join('') }}
                                    </div>
                                    <div class="space-y-1 flex-1 max-w-[80%]">
                                        <div class="flex items-baseline gap-2 justify-start">
                                            <h5 class="font-semibold text-sm text-gray-900">{{ $msg->user_name ?? 'You' }}</h5>
                                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($msg->sent_at)->format('M d, Y, g:i A') }}</span>
                                        </div>
                                        <div class="inline-block p-3 px-4 rounded-2xl text-sm leading-relaxed text-left bg-gray-100 text-gray-800 border border-gray-100">
                                            {{ $msg->message }}
                                        </div>
                                    </div>
                                </div>

                            {{-- Admin / Agent Reply --}}
                            @else
                                <div class="flex gap-4 flex-row-reverse">
                                    <div class="w-9 h-9 bg-blue-600 text-white font-bold text-xs rounded-full flex items-center justify-center shadow-md flex-shrink-0">
                                        {{ collect(explode(' ', $msg->user_name ?? 'Admin User'))->map(fn($n) => strtoupper(substr($n, 0, 1)))->join('') }}
                                    </div>
                                    <div class="space-y-1 flex-1 max-w-[80%] text-right">
                                        <div class="flex items-baseline gap-2 justify-end">
                                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($msg->sent_at)->format('g:i A') }}</span>
                                            <h5 class="font-semibold text-sm text-gray-900">{{ $msg->user_name ?? 'Admin User' }}</h5>
                                            <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded">Admin</span>
                                        </div>
                                        <div class="inline-block p-3 px-4 rounded-2xl text-sm leading-relaxed text-left bg-blue-600 text-white border border-blue-700">
                                            {{ $msg->message }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                        @endforelse

                    </div>

                    <!-- Reply Form -->
                    <div class="animate-reveal-card bg-white border border-gray-200 rounded-3xl p-6 shadow-sm space-y-4" style="--card-index: {{ count($replies) + 3 }};">
                        <h3 class="text-lg font-bold text-gray-900">Add a reply</h3>
                        <form action="{{ route('customer.tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            
                            <div>
                                <textarea 
                                    name="body"
                                    rows="4" 
                                    placeholder="Add more details, ask a follow-up, or provide additional information..." 
                                    class="w-full p-4 text-sm bg-white border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none transition-all"
                                ></textarea>
                            </div>
                            
                            <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                                <label class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-full transition-all cursor-pointer">
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i> 
                                    <span id="attachLabel">Attach file</span>
                                    <input type="file" name="attachments[]" multiple class="hidden" onchange="handleFileSelect(this)">
                                </label>
                                <span id="fileCount" class="text-xs text-gray-500 font-medium"></span>
                                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow active:scale-[0.98] transition-all">
                                    Send Reply
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Attachments appear in the chat thread -->
                    @if($attachments && $attachments->count() > 0)
                    <div class="animate-reveal-card bg-white border border-blue-100 rounded-3xl shadow-[0_2px_12px_rgba(0,0,0,0.01)] overflow-hidden" style="--card-index: {{ count($replies) + 4 }};">
                        <div class="bg-blue-50/50 px-6 py-4 border-b border-blue-100 flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                <i data-lucide="paperclip" class="w-4 h-4"></i>
                            </div>
                            <h4 class="font-bold text-sm text-gray-900">Attached Files ({{ $attachments->count() }})</h4>
                        </div>
                        <div class="p-6 space-y-3">
                            @foreach($attachments as $file)
                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-100 rounded-xl hover:bg-blue-50/30 transition group">
                                <div class="w-10 h-10 bg-white border border-gray-100 rounded-lg flex items-center justify-center text-blue-500 shadow-sm">
                                    <i data-lucide="file" class="w-5 h-5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $file->filename }}</p>
                                    <p class="text-[11px] text-gray-400">Click to download/view</p>
                                </div>
                                <i data-lucide="external-link" class="w-4 h-4 text-gray-400 group-hover:text-blue-600 transition"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                <!-- RIGHT COLUMN -->
                <div class="space-y-6">
                    
                    <!-- Ticket Details -->
                    <div class="animate-reveal-card bg-white border border-gray-200/90 rounded-3xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.01)] space-y-5" style="--card-index: 2;">
                        <h4 class="font-extrabold text-gray-800 text-sm tracking-wide border-b border-gray-50 pb-2">Ticket details</h4>
                        <div class="space-y-4 text-xs font-medium">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Ticket ID</span>
                                <span class="font-bold text-gray-800">TKT-{{ $ticket->id }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Status</span>
                                <span class="px-3 py-0.5 border {{ $statusClass }} bg-white rounded-md font-bold text-[11px] capitalize">{{ $ticket->status ?? 'Open' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Category</span>
                                <span class="font-bold text-gray-800">{{ $ticket->category ?? 'General Support' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Priority</span>
                                <span class="font-bold text-gray-800">{{ $ticket->priority ?? 'Normal' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Created</span>
                                <span class="font-bold text-gray-600">{{ $ticket->created_at ? $ticket->created_at->format('F d, Y') : '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Last Updated</span>
                                <span class="font-bold text-gray-600">{{ $ticket->updated_at ? $ticket->updated_at->format('F d, Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Agent -->
                    @if($agent)
                    <div class="animate-reveal-card bg-white border border-gray-200/90 rounded-3xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.01)] space-y-4" style="--card-index: 3;">
                        <h4 class="font-extrabold text-gray-800 text-sm tracking-wide">Your Agent</h4>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 text-blue-700 font-black text-xs rounded-2xl flex items-center justify-center shadow-sm">
                                {{ collect(explode(' ', $agent->name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->join('') }}
                            </div>
                            <div>
                                <p class="font-extrabold text-sm text-gray-900 leading-snug">{{ $agent->name }}</p>
                                <p class="text-[11px] text-gray-400 font-semibold mt-0.5">Support Specialist</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Expected Resolution -->
                    @if(isset($ticket->expected_resolution))
                    <div class="animate-reveal-card bg-amber-50/40 border border-amber-200/60 rounded-3xl p-6 shadow-sm space-y-1.5" style="--card-index: 4;">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-700/80 block">Expected Resolution</span>
                        <h4 class="text-xl font-black text-amber-900 tracking-tight">{{ \Carbon\Carbon::parse($ticket->expected_resolution)->format('D, F d') }}</h4>
                        <p class="text-xs font-bold text-amber-700/80">{{ \Carbon\Carbon::parse($ticket->expected_resolution)->format('g:i A') }}</p>
                    </div>
                    @endif

                    <!-- Attachments -->
                    @if($attachments && $attachments->count() > 0)
                    <div class="animate-reveal-card bg-white border border-gray-200/90 rounded-3xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.01)] space-y-4" style="--card-index: 5;">
                        <h4 class="font-extrabold text-gray-800 text-sm tracking-wide">Case Attachments ({{ $attachments->count() }})</h4>
                        <div class="space-y-2.5">
                            @foreach($attachments as $file)
                            <div class="flex items-center justify-between p-3 bg-gray-50/50 border border-gray-100 rounded-2xl hover:bg-gray-50/80 transition group cursor-pointer">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl {{ str_contains($file->file_name ?? $file->name ?? '', '.pdf') ? 'bg-amber-50 text-amber-600 border-amber-100/40' : 'bg-blue-50 text-[#0f62fe] border-blue-100/40' }} flex items-center justify-center shrink-0">
                                        <i data-lucide="{{ str_contains($file->file_name ?? $file->name ?? '', '.pdf') ? 'file-text' : 'image' }}" class="w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block text-xs font-bold text-gray-800 truncate group-hover:text-[#0f62fe] transition">{{ $file->file_name ?? $file->name ?? 'Attachment' }}</span>
                                        <span class="text-[10px] text-gray-400 font-semibold block mt-0.5">{{ $file->file_size ?? '-' }}</span>
                                    </div>
                                </div>
                                <i data-lucide="download" class="w-4 h-4 text-gray-400 group-hover:text-gray-700 mr-1 transition-transform group-hover:scale-105"></i>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Back Button -->
                    <a href="{{ route('customer.tickets') }}" class="animate-reveal-card flex items-center justify-center gap-2 w-full py-3.5 bg-white border border-gray-200 hover:border-gray-300 rounded-2xl text-xs font-bold text-gray-500 hover:text-gray-800 shadow-[0_2px_10px_rgba(0,0,0,0.01)] transition active:scale-[0.99]" style="--card-index: 6;">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to My Tickets
                    </a>

                </div>
            </div>
        </main>

        <footer class="bg-white border-t border-gray-100 w-full shrink-0">
            <div class="max-w-6xl mx-auto px-8 h-16 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-400 font-medium">
                <p>&copy; 2026 Support Center. All rights reserved.</p>
                <div class="flex space-x-6 mt-2 sm:mt-0">
                    <a href="{{ route('agent') }}" class="hover:text-gray-600 transition">Agent Portal</a>
                    <a href="#" class="hover:text-gray-600 transition">FAQ</a>
                    <a href="{{ route('terms') }}" class="hover:text-gray-600 transition">Terms</a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });

        function handleFileSelect(input) {
            const count = input.files.length;
            const label = document.getElementById('attachLabel');
            const countLabel = document.getElementById('fileCount');
            
            if (count > 0) {
                label.textContent = count === 1 ? input.files[0].name : `${count} files selected`;
                countLabel.textContent = '';
            } else {
                label.textContent = 'Attach file';
                countLabel.textContent = '';
            }
        }
    </script>
</body>
</html>