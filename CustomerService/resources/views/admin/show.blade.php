@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div>
                    <h1 class="h2">Ticket #{{ $ticket->ticket_reference }}</h1>
                    <p class="text-muted mb-0">{{ $ticket->subject }}</p>
                </div>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('admin.support.tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Tickets
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Ticket Details -->
                <div class="col-lg-8">
                    <!-- Status Card -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Ticket Information</span>
                            <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'resolved' ? 'primary' : 'secondary') }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Category</label>
                                    <p class="mb-1"><strong>{{ $ticket->category }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Priority</label>
                                    <p class="mb-1">
                                        <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Created</label>
                                    <p class="mb-1">{{ $ticket->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Due Date</label>
                                    <p class="mb-1">{{ $ticket->due_date ? $ticket->due_date->format('M d, Y H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Customer</label>
                                    <p class="mb-1">{{ $ticket->user->name ?? 'Unknown' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Assigned Agent</label>
                                    <p class="mb-1">{{ $ticket->agent->name ?? 'Unassigned' }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="text-muted small">Description</label>
                                <div class="p-3 bg-light rounded">
                                    {{ $ticket->description }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Replies Section -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-chat-dots"></i> Replies ({{ $ticket->replies->count() }})
                        </div>
                        <div class="card-body">
                            @forelse($ticket->replies as $reply)
                                <div class="d-flex mb-3 {{ $reply->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-{{ $reply->user->role === 'admin' ? 'danger' : 'primary' }} text-white d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; font-size: 14px;">
                                            {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 mx-3">
                                        <div class="card {{ $reply->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                                            <div class="card-body py-2 px-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="{{ $reply->user_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                                        <strong>{{ $reply->user->name ?? 'Unknown' }}</strong>
                                                        @if($reply->user->role === 'admin')
                                                            <span class="badge bg-warning text-dark ms-1">Admin</span>
                                                        @endif
                                                    </small>
                                                    <small class="{{ $reply->user_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                                        {{ $reply->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                                <p class="mb-0">{{ $reply->message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">No replies yet. Be the first to respond!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Reply Form -->
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-reply"></i> Add Reply
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.support.tickets.reply', $ticket->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" 
                                              rows="4" placeholder="Type your reply here..." required></textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="resolve_ticket" id="resolveTicket">
                                        <label class="form-check-label" for="resolveTicket">
                                            Mark as resolved
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Send Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: SLA & Actions -->
                <div class="col-lg-4">
                    <!-- SLA Metrics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-clock-history"></i> SLA Metrics
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">First Response Time</label>
                                <p class="mb-1">
                                    @if($ticket->first_response_at)
                                        <span class="text-success">
                                            <i class="bi bi-check-circle"></i> 
                                            {{ $ticket->first_response_at->diffForHumans($ticket->created_at) }}
                                        </span>
                                    @else
                                        <span class="text-warning">
                                            <i class="bi bi-hourglass-split"></i> Awaiting first response
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Resolution Time</label>
                                <p class="mb-1">
                                    @if($ticket->resolved_at)
                                        <span class="text-success">
                                            <i class="bi bi-check-circle"></i> 
                                            {{ $ticket->resolved_at->diffForHumans($ticket->created_at) }}
                                        </span>
                                    @else
                                        <span class="text-warning">
                                            <i class="bi bi-hourglass-split"></i> Not resolved yet
                                        </span>
                                    @endif
                                </p>
                            </div>
                            @if($ticket->response_time_minutes)
                            <div class="mb-3">
                                <label class="text-muted small">Response Time (minutes)</label>
                                <p class="mb-1">{{ number_format($ticket->response_time_minutes) }}</p>
                            </div>
                            @endif
                            @if($ticket->resolution_time_minutes)
                            <div class="mb-3">
                                <label class="text-muted small">Resolution Time (minutes)</label>
                                <p class="mb-1">{{ number_format($ticket->resolution_time_minutes) }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-lightning"></i> Quick Actions
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.support.tickets.assign', $ticket->id) }}" method="POST" class="mb-3">
                                @csrf
                                <label class="text-muted small mb-2">Assign to Agent</label>
                                <div class="input-group">
                                    <select name="agent_id" class="form-select">
                                        <option value="">-- Select Agent --</option>
                                        @foreach($agents ?? [] as $agent)
                                            <option value="{{ $agent->id }}" {{ $ticket->agent_id == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-primary" type="submit">Assign</button>
                                </div>
                            </form>

                            <div class="d-grid gap-2">
                                @if($ticket->status !== 'resolved')
                                    <form action="{{ route('admin.support.tickets.resolve', $ticket->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-check-lg"></i> Mark as Resolved
                                        </button>
                                    </form>
                                @endif
                                
                                @if($ticket->status !== 'closed')
                                    <form action="{{ route('admin.support.tickets.close', $ticket->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-secondary w-100">
                                            <i class="bi bi-x-lg"></i> Close Ticket
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.support.tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash"></i> Delete Ticket
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-person"></i> Customer Details
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>{{ $ticket->user->name ?? 'N/A' }}</strong></p>
                            <p class="text-muted small mb-2">{{ $ticket->user->email ?? 'N/A' }}</p>
                            <hr>
                            <a href="{{ route('admin.customers.show', $ticket->user_id) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-person-lines-fill"></i> View Customer Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection