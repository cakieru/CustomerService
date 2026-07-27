<!DOCTYPE html>
<html>
<head>
    <title>Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">All Tickets</h1>
        
        <a href="{{ route('tickets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">Create New Ticket</a>
        
        <table class="w-full bg-white rounded-xl shadow">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3 text-left">Ticket #</th>
                    <th class="p-3 text-left">Customer</th>
                    <th class="p-3 text-left">Priority</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Created</th>
                    <th class="p-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr class="border-b">
                    <td class="p-3">{{ $ticket->ticket_number }}</td>
                    <td class="p-3">{{ $ticket->customer_name }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs 
                            @if($ticket->priority_level === 'Critical') bg-red-100 text-red-800
                            @elseif($ticket->priority_level === 'High') bg-orange-100 text-orange-800
                            @elseif($ticket->priority_level === 'Medium') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $ticket->priority_level }}
                        </span>
                    </td>
                    <td class="p-3">{{ $ticket->status }}</td>
                    <td class="p-3">{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                    <td class="p-3">
                        <form method="POST" action="{{ route('tickets.updateStatus', $ticket) }}" class="inline">
                            @csrf
                            @method('PUT')
                            
                            @if($ticket->status === 'Open')
                                <button type="submit" name="status" value="In Progress" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">Start</button>
                            @elseif($ticket->status === 'In Progress')
                                <button type="submit" name="status" value="Resolved" class="bg-green-500 text-white px-3 py-1 rounded text-sm">Resolve</button>
                            @endif
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>