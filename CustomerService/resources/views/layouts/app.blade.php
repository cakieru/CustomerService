<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:wght@100..1000&display=swap" rel="stylesheet">
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
        body, * {
            font-family: 'Google Sans Flex', sans-serif !important;
        }
        @keyframes toastPop {
            0% { opacity: 0; transform: scale(0.92); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
    
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col">

    <!-- Header copied directly from CustomerPortal -->
    <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Logo -->
            <div onclick="resetPortalHome()" class="flex items-center space-x-3 group cursor-pointer select-none">
                <div class="bg-[#0f4c81] text-white p-2.5 rounded-xl shadow-sm group-hover:scale-105 transition-transform duration-300">
                    <i data-lucide="headphones" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-gray-800 leading-tight">Support Center</h1>
                    <p class="text-xs text-gray-400 font-medium">We're here to help</p>
                </div>
            </div>
            
            <!-- Navigation Links -->
            <nav class="flex items-center space-x-8">
                <a href="{{ route('CustomerPortal') }}" class="text-gray-500 hover:text-gray-800 font-medium text-sm transition-colors duration-300">Home</a>
                <a href="{{ route('customer.tickets') }}" class="text-gray-500 hover:text-gray-800 font-medium text-sm transition-colors duration-300">My Tickets</a> 
                <a href="{{ route('customer.create') }}" class="bg-[#0f62fe] hover:bg-[#0052cc] text-white font-semibold px-5 py-2.5 rounded-xl text-sm shadow-sm hover:shadow-md flex items-center space-x-1.5 transition-all duration-300 hover:-translate-y-0.5 active:translate-y-0">
                    <span>+ New Request</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-1 max-w-5xl w-full mx-auto px-6 pt-1 pb-12 flex flex-col justify-between space-y-12">
        @yield('content')

        <!-- Footer copied directly from CustomerPortal -->
        <div class="flex items-center justify-between text-xs text-slate-400 border-t border-slate-200 pt-8 !mt-12">
            <p>&copy; 2026 Support Center. All rights reserved.</p>
            <div class="space-x-4">
                <a href="{{ route('admin.support.dashboard') }}" class="hover:underline">Agent Portal</a>
                <a href="#" class="hover:text-gray-600 transition">FAQ</a>
                <a href="#" class="hover:text-gray-600 transition">Terms</a>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>