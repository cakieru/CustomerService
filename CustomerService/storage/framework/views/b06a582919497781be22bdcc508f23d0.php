<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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

    <main class="flex-grow flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto w-full">
        <?php echo $__env->yieldContent('content'); ?>
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
</html>

<?php /**PATH C:\Users\Administrator\Documents\GitHub\CustomerService\CustomerService\resources\views/layouts/app.blade.php ENDPATH**/ ?>