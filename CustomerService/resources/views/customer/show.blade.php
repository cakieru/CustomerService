@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <!-- Breadcrumb -->
    <div class="text-xs text-gray-400 mb-4 font-medium">
        <a href="{{ url('/') }}" class="hover:text-gray-600">Home</a> &gt; <a href="{{ url('/tickets') }}" class="hover:text-gray-600">My Tickets</a> &gt; <span class="text-gray-600">Ticket Details</span>
    </div>

    @php
        $status = strtolower($ticket->status ?? 'open');
        $badgeStyle = 'bg-[#FFFBEB] text-[#D97706] border-[#FDE68A]';
        if (in_array($status, ['in progress', 'in_progress', 'processing'])) {
            $badgeStyle = 'bg-[#EFF6FF] text-[#2563EB] border-[#BFDBFE]';
        } elseif ($status == 'resolved') {
            $badgeStyle = 'bg-[#ECFDF5] text-[#059669] border-[#A7F3D0]';
        } elseif ($status == 'closed') {
            $badgeStyle = 'bg-gray-100 text-gray-600 border-gray-200';
        }
    @endphp

    <!-- Top Ticket Summary Header Card -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mb-6">
        <div class="text-xs text-gray-400 font-medium mb-1.5 flex items-center space-x-2">
            <span>TKT-{{ $ticket->id }}</span>
            <span>•</span>
            <span>{{ $ticket->category ?? 'General Support' }}</span>
            <span>•</span>
            <span>Opened {{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y, g:i A') : 'Recently' }}</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $ticket->subject ?? $ticket->title ?? 'Support Request #' . $ticket->id }}</h1>
        
        <div class="flex flex-wrap items-center gap-4 text-xs font-medium">
            <span class="px-3 py-1 border rounded-full font-bold capitalize {{ $badgeStyle }}">{{ $ticket->status ?? 'Open' }}</span>
            
            @if(isset($ticket->priority))
                <div class="flex items-center space-x-1.5 text-gray-600 capitalize">
                    <span class="w-2 h-2 rounded-full {{ strtolower($ticket->priority) == 'high' ? 'bg-red-500' : 'bg-blue-500' }} inline-block"></span>
                    <span>{{ $ticket->priority }} Priority</span>
                </div>
            @endif

            @if(isset($ticket->agent))
                <div class="flex items-center space-x-2 text-gray-600">
                    <div class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center">
                        {{ substr($ticket->agent->name, 0, 1) }}
                    </div>
                    <span>Assigned to {{ $ticket->agent->name }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 2-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- LEFT COLUMN: Conversation & Replies -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Original Customer Message Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center">
                            {{ auth()->check() ? substr(auth()->user()->name, 0, 2) : 'YOU' }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">{{ auth()->user()->name ?? 'You' }} <span class="text-gray-400 font-normal text-xs">— Original Message</span></h4>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">{{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y, g:i A') : '' }}</span>
                </div>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $ticket->description ?? $ticket->message ?? $ticket->body ?? 'No message body provided.' }}</p>
            </div>

            <!-- DYNAMIC REPLIES LOOP (Step 4.B - Pinalitan para sa customer_conversations) -->
            @foreach($replies as $reply)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm {{ $reply->sender !== 'Customer' ? 'border-l-4 border-l-indigo-500' : '' }}">
                    <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full {{ $reply->sender !== 'Customer' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700' }} font-bold text-xs flex items-center justify-center">
                                {{ substr($reply->sender, 0, 1) }}
                            </div>
                            <h4 class="text-sm font-bold text-gray-900">
                                {{ $reply->sender == 'Customer' ? (auth()->user()->name ?? 'You') : ($reply->sender == 'System' ? 'Support Assistant' : 'Support Agent') }}
                                
                                @if($reply->sender == 'System')
                                    <span class="ml-1 px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-semibold">Automated</span>
                                @elseif($reply->sender == 'Agent')
                                    <span class="ml-1 px-1.5 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[10px] font-semibold">Staff</span>
                                @endif
                            </h4>
                        </div>
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($reply->sent_at)->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $reply->message }}</p>
                </div>
            @endforeach

            <!-- FUNCTIONAL REPLY BOX (Step 4.A - Inayos ang form inputs at route) -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Add a reply</h3>
                
                <form action="{{ route('tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Hinihingi ng Controller para malaman kung sino ang nag-reply -->
                    <input type="hidden" name="sender_type" value="Customer">

                    <!-- Binago ang name attribute mula 'body' patungong 'message' -->
                    <textarea name="message" rows="4" required placeholder="Add more details, ask a follow-up, or provide additional information..." class="w-full p-3.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white resize-none mb-3"></textarea>
                                    
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center space-x-1.5 px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            <span>Attach file</span>
                            <input type="file" name="attachment" class="hidden">
                        </label>
                                        
                        <button type="submit" class="px-6 py-2 bg-[#A5B4FC] hover:bg-[#818CF8] text-white text-xs font-bold rounded-xl shadow-sm transition">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>

            <!-- Empty State Notice kung wala pang laman ang replies variable -->
            @if($replies->isEmpty())
                <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center shadow-sm">
                    <p class="text-xs font-bold text-gray-700 mb-0.5">No replies yet - our team is reviewing your request.</p>
                    <p class="text-[11px] text-gray-400">You'll receive an email notification when we respond.</p>
                </div>
            @endif

        </div>

        <!-- RIGHT COLUMN: Metadata Sidebar -->
        <div class="space-y-4">
            
            <!-- Ticket Details Table -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <h4 class="text-xs font-bold text-gray-900 mb-3">Ticket details</h4>
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-400">Ticket ID</span>
                        <span class="font-medium text-gray-800">#{{ $ticket->id }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-50">
                        <span class="text-gray-400">Status</span>
                        <span class="px-2.5 py-0.5 border rounded-full text-[10px] font-bold capitalize {{ $badgeStyle }}">{{ $ticket->status ?? 'Open' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-400">Category</span>
                        <span class="font-medium text-gray-800">{{ $ticket->category ?? 'General' }}</span>
                    </div>
                    @if(isset($ticket->priority))
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-400">Priority</span>
                            <span class="font-medium text-gray-800 capitalize">{{ $ticket->priority }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-400">Created</span>
                        <span class="font-medium text-gray-800">{{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-gray-400">Last Updated</span>
                        <span class="font-medium text-gray-800">{{ $ticket->updated_at ? \Carbon\Carbon::parse($ticket->updated_at)->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Dynamic Assigned Agent Card -->
            @if(isset($ticket->agent))
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-900 mb-3">Your Agent</h4>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                            {{ substr($ticket->agent->name, 0, 2) }}
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-gray-900">{{ $ticket->agent->name }}</h5>
                            <p class="text-[11px] text-gray-400">Support Specialist</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Dynamic Attachments Display -->
            @if(isset($ticket->attachments) && count($ticket->attachments) > 0)
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h4 class="text-xs font-bold text-gray-900 mb-3">Case Attachments ({{ count($ticket->attachments) }})</h4>
                    <div class="space-y-2">
                        @foreach($ticket->attachments as $attachment)
                            <a href="{{ url('/attachments/' . $attachment->id) }}" target="_blank" class="flex items-center justify-between p-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-100 rounded-xl transition text-xs group">
                                <div class="flex items-center space-x-2.5 truncate">
                                    <div class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    </div>
                                    <div class="truncate">
                                        <p class="font-medium text-gray-800 truncate">{{ $attachment->filename ?? 'Attachment' }}</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <a href="{{ url('/tickets') }}" class="w-full py-2.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-xl shadow-sm transition flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Back to My Tickets</span>
            </a>

        </div>

    </div>
</div>
@endsection