<!DOCTYPE html>
<html>
<head>
    <title>Create Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Create New Ticket</h1>
        
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Customer Name</label>
                <input type="text" name="customer_name" class="w-full border rounded px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Issue Description</label>
                <textarea name="issue_description" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Priority Level</label>
                <select name="priority_level" class="w-full border rounded px-3 py-2" required>
                    <option value="Critical">Critical (4h target)</option>
                    <option value="High">High (8h target)</option>
                    <option value="Medium">Medium (16h target)</option>
                    <option value="Low">Low (24h target)</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Create Ticket
            </button>
        </form>
    </div>
</body>
</html>