<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Support Center - Customer Portal' }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Premium Fonts Link -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:wght@100..1000&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
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

   <!-- Custom Animation Utilities -->
   <style>
        body, * {
            font-family: 'Google Sans Flex', 'sans-serif' !important;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animation-delay-100 { animation-delay: 100ms; }
        .animation-delay-200 { animation-delay: 200ms; }
        .animation-delay-300 { animation-delay: 300ms; }
    </style>
</head>
<body class="bg-[#fafafa] font-sans antialiased text-[#2d3748] min-h-screen flex flex-col justify-between selection:bg-blue-100">
 <!-- Amparo 8:47 -->
    <!-- Navbar -->
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
                <button onclick="resetPortalHome()" class="bg-[#f0f4f8] text-[#0f4c81] font-semibold px-4 py-2 rounded-xl text-sm transition-all duration-300 hover:opacity-90">Home</button>
                <a href="{{ route('customer.tickets') }}" class="text-gray-500 hover:text-gray-800 font-medium text-sm transition-colors duration-300">My Tickets</a> 
                <a href="{{ route('customer.create') }}" class="bg-[#0f62fe] hover:bg-[#0052cc] text-white font-semibold px-5 py-2.5 rounded-xl text-sm shadow-sm hover:shadow-md flex items-center space-x-1.5 transition-all duration-300 hover:-translate-y-0.5 active:translate-y-0">
                    <span>+ New Request</span>
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="max-w-6xl mx-auto px-6 py-12 w-full flex-grow flex flex-col justify-between space-y-12">
        
        <!-- Hero Section -->
        <div id="portalHeroSection" class="relative z-20 text-center max-w-2xl mx-auto opacity-0 animate-fade-in-up w-full block">
            <!-- Badge -->
            <div class="inline-flex items-center space-x-1.5 bg-[#f0f4f8] text-[#0f4c81] px-4 py-1.5 rounded-full text-xs font-semibold mb-6 hover:bg-blue-100 transition-colors duration-300 cursor-default">
                <i data-lucide="zap" class="w-3.5 h-3.5 fill-[#0f4c81] animate-pulse"></i>
                <span>Average response time: under 2 hours</span>
            </div>
            
            <h2 class="text-4xl font-extrabold text-gray-950 tracking-tight mb-4">How can we help you?</h2>
            <p class="text-gray-500 font-normal text-lg mb-8">Search our knowledge base to find quick answers.</p>
            
            <!-- Search Bar with suggestions dropdown -->
            <div class="relative max-w-xl mx-auto shadow-sm hover:shadow-md focus-within:shadow-md transition-shadow duration-300 rounded-2xl text-left">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </span>
                <input type="text" 
                       id="heroSearchInput" 
                       oninput="handleLiveSearch(this.value)" 
                       onkeydown="if(event.key === 'Enter') handleHeroSearch(this.value)" 
                       placeholder="Search articles, FAQs, guides..." 
                       class="w-full bg-white pl-12 pr-4 py-4 rounded-2xl border border-gray-200 outline-none focus:border-[#0f62fe] focus:ring-4 focus:ring-blue-100/50 transition-all duration-300 text-gray-700 placeholder-gray-400" />
                
                <div id="searchSuggestionsDropdown" class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl hidden z-40 max-h-60 overflow-y-auto text-left divide-y divide-gray-100"></div>
            </div>
        </div>

        <!-- Three Core Action Cards -->
        <div id="portalPillarCards" class="relative z-0 grid grid-cols-1 md:grid-cols-3 gap-6 opacity-0 animate-fade-in-up animation-delay-100 block">
            <!-- Submit a Request -->
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div>
                    <div class="bg-[#edf2ff] text-[#4c6ef5] p-3 rounded-2xl w-fit mb-5 group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="ticket" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Submit a Request</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Create a new support ticket and our team will respond shortly.</p>
                </div>
                <a href="{{ route('customer.create') }}" class="text-[#0f62fe] font-semibold text-sm flex items-center space-x-1">
                    <span>Get started</span>
                    <span class="transform group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                </a>
            </div>

           <!-- Track My Tickets -->
            <a href="{{ route('customer.tickets') }}" class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div>
                    <div class="bg-[#ebfbee] text-[#40c057] p-3 rounded-2xl w-fit mb-5 group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Track My Tickets</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Check the status of your open requests and view replies.</p>
                </div>
                <div class="text-[#40c057] font-semibold text-sm flex items-center space-x-1">
                    <span>View Tickets</span>
                    <span class="transform group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                </div>
            </a>

            <!-- Knowledge Base -->
            <div onclick="switchView('ARTICLES')" class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                <div>
                    <div class="bg-[#fff9db] text-[#f59f00] p-3 rounded-2xl w-fit mb-5 group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="book-open" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Knowledge Base</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Browse guides, FAQs, and how-to articles to find quick answers.</p>
                </div>
                <div class="text-[#f59f00] font-semibold text-sm flex items-center space-x-1">
                    <span>Browse Articles</span>
                    <span class="transform group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                </div>
            </div>
        </div>

        <!-- Popular Topics Section -->
        <div id="portalPopularTopicsWrapper" class="opacity-0 animate-fade-in-up animation-delay-200 block">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Popular topics</h3>
            <div id="popularTopicsGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
        </div>

        <!-- Filtered Category Stream -->
        <div id="filteredArticlesContainer" class="space-y-6 hidden"></div>

        <!-- Comprehensive Knowledge Base View -->
        <div id="portalArticlesView" class="space-y-6 hidden">
            <nav class="text-xs text-gray-400 flex items-center space-x-2 mb-2 select-none">
                <button onclick="resetPortalHome()" class="hover:underline flex items-center gap-1 text-gray-400 font-medium"><i data-lucide="home" class="w-3 h-3"></i> Home</button>
                <span class="text-gray-300">&rsaquo;</span>
                <span class="text-gray-500 font-medium">View Articles</span>
            </nav>
            <div class="pb-2">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Knowledge Base</h1>
                <p class="text-xs text-gray-500 mt-0.5 font-medium">Self service portal for customers and support agents</p>
            </div>
            <div id="fullKnowledgeBaseStream" class="space-y-6"></div>
        </div>

        <!-- FAQ View Container -->
        <div id="portalFAQView" class="space-y-6 hidden">
            <nav class="text-xs text-gray-400 flex items-center space-x-2 mb-2 select-none">
                <button onclick="resetPortalHome()" class="hover:underline flex items-center gap-1 text-gray-400 font-medium"><i data-lucide="home" class="w-3 h-3"></i> Home</button>
                <span class="text-gray-300">&rsaquo;</span>
                <span class="text-gray-500 font-medium">FAQs</span>
            </nav>
            <div class="pb-2"><h1 class="text-3xl font-bold text-gray-900 tracking-tight">Frequently Asked Questions</h1></div>
            
            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm space-y-8 text-gray-700">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 font-bold text-gray-900 text-lg">
                        <i data-lucide="monitor" class="w-5 h-5 text-gray-800"></i>
                        <h2>General & Products Question</h2>
                    </div>
                    <div class="pl-2 space-y-4">
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-gray-400 font-normal text-base leading-none">•</span> Are all your components brand new?</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pl-4">Yes, we only sell brand-new, factory-sealed products sourced directly from authorized manufacturers and distributors. Every item includes the original manufacturer's warranty.</p>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-slate-400 font-normal text-base leading-none">•</span> Do you offer pre-built PCs, or just individual parts?</h3>
                            <p class="text-xs text-slate-500 leading-relaxed pl-4">We offer both! We have a curated selection of pre-built gaming and workstation PCs, as well as a "Build Your Own" option where you pick the parts and our expert technicians assemble and test it for you before shipping.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pt-2">
                    <div class="flex items-center space-x-3 font-bold text-gray-900 text-lg">
                        <i data-lucide="truck" class="w-5 h-5 text-gray-800"></i>
                        <h2>Shipping & Delivery</h2>
                    </div>
                    <div class="pl-2 space-y-4">
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-gray-400 font-normal text-base leading-none">•</span> How long will it take for my order to arrive?</h3>
                            <div class="text-xs text-gray-500 leading-relaxed pl-4 space-y-0.5">
                                <p>Standard Shipping: 3–5 business days.</p>
                                <p>Express Shipping: 1–2 business days.</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-gray-400 font-normal text-base leading-none">•</span> Do you ship internationally?</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pl-4">Currently, we ship nationwide. We are working on expanding our shipping options to international tech enthusiasts soon!</p>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-gray-400 font-normal text-base leading-none">•</span> Are shipments insured? Computer parts are fragile.</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pl-4">Absolutely. All shipments—especially high-value items like GPUs and custom PCs—are fully insured and packed with specialized anti-static, shock-absorbing materials to ensure they arrive safely.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pt-2">
                    <div class="flex items-center space-x-3 font-bold text-gray-900 text-lg">
                        <i data-lucide="credit-card" class="w-5 h-5 text-gray-800"></i>
                        <h2>Payment & Ordering</h2>
                    </div>
                    <div class="pl-2 space-y-4">
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-gray-400 font-normal text-base leading-none">•</span> What payment methods do you accept?</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pl-4">We mainly accept Cash on Delivery orders, we also accept all major credit/debit cards (BDO, Visa, and Mastercard) and E - Wallets like PayPal, G Cash, and Maya.</p>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-gray-400 font-normal text-base leading-none">•</span> Can I cancel or modify my order after placing it?</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pl-4">Because we aim to ship orders as quickly as possible, you can only cancel or modify an order within 30 minutes of placing it. Please contact our live chat or support hotline immediately for urgent changes.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pt-2">
                    <div class="flex items-center space-x-3 font-bold text-gray-900 text-lg">
                        <i data-lucide="refresh-cw" class="w-5 h-5 text-gray-800"></i>
                        <h2>Returns, Warranties, & Technical Support</h2>
                    </div>
                    <div class="pl-2 space-y-4">
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-gray-400 font-normal text-base leading-none">•</span> What is your return policy?</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pl-4">We offer a 30-day return policy for most unopened items. If an item has been opened but is defective, you can return it for a full refund or replacement.</p>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-gray-400 font-normal text-base leading-none">•</span> What should I do if my component arrives Damaged?</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pl-4">Let us know within 48 hours. Send us your order number and a photo or video of the issue, and we will arrange a free return and rush you a replacement.</p>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-gray-900 flex items-start gap-2"><span class="text-gray-400 font-normal text-base leading-none">•</span> How does the warranty work?</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pl-4">Every item is covered by its respective manufacturer's warranty (typically 1 to 10 years depending on the component, like power supplies or RAM). For the first 30 days, we handle defects directly. After 30 days, we can assist you in contacting the manufacturer for a Warranty Claim (RMA).</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center py-2 text-[11px] text-gray-400 select-none font-medium">You've reached the end.</div>
        </div>

        <!-- Terms View Container -->
        <div id="portalTermsView" class="space-y-6 hidden">
            <nav class="text-xs text-gray-400 flex items-center space-x-2 mb-2 select-none">
                <button onclick="resetPortalHome()" class="hover:underline flex items-center gap-1 text-gray-400 font-medium"><i data-lucide="home" class="w-3 h-3"></i> Home</button>
                <span class="text-gray-300">&rsaquo;</span>
                <span class="text-gray-500 font-medium">Terms & Conditions</span>
            </nav>
            <div class="pb-2"><h1 class="text-3xl font-bold text-gray-900 tracking-tight">Terms and Conditions</h1></div>

            <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm space-y-8 text-gray-700">
                <p class="text-sm text-gray-500 leading-relaxed">These Terms & Conditions govern your use of our Support Center and the services provided through our ticketing system. By using our system, you agree to the following terms.</p>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center space-x-3 font-bold text-gray-900 text-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-gray-800"></i>
                        <h2>1. Use of Our Support Center</h2>
                    </div>
                    <div class="pl-2 space-y-2">
                        <p class="text-xs text-gray-500 leading-relaxed">Our support center is provided to assist customers with inquiries, issues, and support related to our products and services.</p>
                        <ul class="space-y-1 text-xs text-gray-500 leading-relaxed pl-1">
                            <li class="flex items-start gap-2"><span class="text-gray-400">•</span> You agree to provide accurate and complete information when submitting tickets.</li>
                            <li class="flex items-start gap-2"><span class="text-gray-400">•</span> You will not use the system for any unlawful, abusive, or fraudulent purposes.</li>
                            <li class="flex items-start gap-2"><span class="text-gray-400">•</span> We reserve the right to suspend or restrict access to users who violate these terms.</li>
                        </ul>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center space-x-3 font-bold text-gray-900 text-lg">
                        <i data-lucide="book-open" class="w-5 h-5 text-gray-800"></i>
                        <h2>2. Self-Service Portal</h2>
                    </div>
                    <div class="pl-2 space-y-2">
                        <p class="text-xs text-gray-500 leading-relaxed">Our self-service portal provides resources to help you find solutions independently.</p>
                        <ul class="space-y-1 text-xs text-gray-500 leading-relaxed pl-1">
                            <li class="flex items-start gap-2"><span class="text-gray-400">a.</span> Search database of solutions/articles</li>
                            <li class="flex items-start gap-2"><span class="text-gray-400">b.</span> Categorize help topics</li>
                            <li class="flex items-start gap-2"><span class="text-gray-400">c.</span> User feedback on article helpfulness</li>
                            <li class="flex items-start gap-2"><span class="text-gray-400">d.</span> Reduce ticket volume by promoting self-resolution</li>
                        </ul>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center space-x-3 font-bold text-gray-900 text-lg">
                        <i data-lucide="shield" class="w-5 h-5 text-gray-800"></i>
                        <h2>3. Privacy & Data Protection</h2>
                    </div>
                    <div class="pl-2 space-y-2">
                        <p class="text-xs text-gray-500 leading-relaxed"> We take the privacy of your data seriously and handle it in accordance with applicable data protection laws.</p>
                        <ul class="space-y-1 text-xs text-gray-500 leading-relaxed pl-1">
                            <li class="flex items-start gap-2"><span class="text-gray-400">a.</span> Personal information is only used to process and resolve your requests</li>
                            <li class="flex items-start gap-2"><span class="text-gray-400">b.</span> We do not sell your personal data to third parties</li>
                            <li class="flex items-start gap-2"><span class="text-gray-400">c.</span> Data is stored securely and access is restricted to authorized personnel</li>
                        </ul>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center space-x-3 font-bold text-gray-900 text-lg">
                        <i data-lucide="refresh-cw" class="w-5 h-5 text-gray-800"></i>
                        <h2>4. Changes to These Terms</h2>
                    </div>
                    <div class="pl-2 space-y-2">
                        <p class="text-xs text-gray-500 leading-relaxed">We may update these Terms & Conditions from time to time to reflect changes in our services or legal requirements.</p>
                        <ul class="space-y-1 text-xs text-gray-500 leading-relaxed pl-1">
                            <li class="flex items-start gap-2"><span class="text-gray-400">a.</span> Continued use of our support center after changes means you accept the updated terms</li>
                            <li class="flex items-start gap-2"><span class="text-gray-400">b.</span> Major updates will be communicated via our website or email</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="text-center py-2 text-[11px] text-gray-400 select-none font-medium">You've reached the end.</div>
        </div>

        <!-- Trust Features Footer Banner -->
        <div id="portalSatisfactionBanner" class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-8 items-center px-8 opacity-0 animate-fade-in-up animation-delay-300 hover:shadow-md transition-shadow duration-500 block">
            <!-- Metric 1 -->
            <div class="flex items-start space-x-4 group">
                <div class="bg-green-50 text-green-500 p-2.5 rounded-xl mt-0.5 group-hover:scale-105 transition-transform duration-300">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 text-sm">98.4% Satisfaction</h5>
                    <p class="text-xs text-gray-400 leading-normal mt-0.5">Based on customer feedback from the last 30 days</p>
                </div>
            </div>
            
            <!-- Metric 2 -->
            <div class="flex items-start space-x-4 md:border-x md:border-gray-100 md:px-8 group">
                <div class="bg-blue-50 text-blue-500 p-2.5 rounded-xl mt-0.5 group-hover:scale-105 transition-transform duration-300">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 text-sm">&lt; 2hr Response</h5>
                    <p class="text-xs text-gray-400 leading-normal mt-0.5">Average first reply during business hours</p>
                </div>
            </div>

            <!-- Metric 3 -->
            <div class="flex items-start space-x-4 group">
                <div class="bg-purple-50 text-purple-500 p-2.5 rounded-xl mt-0.5 group-hover:scale-105 transition-transform duration-300">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 text-sm">24/7 Self-Service</h5>
                    <p class="text-xs text-gray-400 leading-normal mt-0.5">Knowledge base always available, agents 9am–6pm</p>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 w-full mt-12">
        <div class="max-w-6xl mx-auto px-6 h-16 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-400 font-medium">
            <p>&copy; {{ date('Y') }} Support Center. All rights reserved.</p>
            <div class="flex space-x-6 mt-2 sm:mt-0">
                <a href="{{ route('admin.support.dashboard') }}" class="hover:text-gray-600 transition-colors duration-300">Agent Portal</a>
                <button onclick="switchView('FAQ')" class="hover:text-gray-600 transition-colors duration-300 focus:outline-none">FAQ</button>
                <button onclick="switchView('TERMS')" class="hover:text-gray-600 transition-colors duration-300 focus:outline-none">Terms</button>    
            </div>
        </div>
    </footer>

    <!-- Core Runtime Scripts -->
    <script>
        @php
            /** @var \Illuminate\Support\Collection $articles */
        @endphp
        
        const articles = {{ \Illuminate\Support\Js::from($articles) }};

        const baseTopics = [
            { id: "shipping", name: "Shipping & Delivery", icon: "truck" },
            { id: "subscriptions", name: "Subscription", icon: "credit-card" },
            { id: "returns", name: "Returns & Refunds", icon: "refresh-cw" },
            { id: "account", name: "Account Management", icon: "key" },
            { id: "damaged", name: "Damaged Items", icon: "shopping-cart" },
            { id: "product", name: "Product Information", icon: "box" }
        ];

        const topics = baseTopics.map(topic => {
            const dynamicCount = articles.filter(art => art.catId === topic.id).length;
            return {
                ...topic,
                count: dynamicCount
            };
        });

        let activeTopicId = null;

        function renderPopularTopicsGrid() {
            const grid = document.getElementById('popularTopicsGrid');
            grid.innerHTML = topics.map(topic => {
                const isSelected = topic.id === activeTopicId;
                const cardClasses = isSelected 
                    ? "bg-[#0f62fe] border-[#0f62fe] text-white shadow-md transform scale-[1.01]" 
                    : "bg-white border-gray-100 text-gray-800 hover:shadow-md hover:border-gray-200 shadow-sm";
                
                const iconContainerClasses = isSelected ? "bg-[#0052cc] text-white" : "text-gray-700 bg-gray-50";
                const textMutedClasses = isSelected ? "text-blue-100" : "text-gray-400";
                const chevronClasses = isSelected ? "text-white" : "text-gray-400";

                return `
                    <div onclick="handleTopicClick('${topic.id}')" class="border rounded-2xl p-5 flex items-center justify-between cursor-pointer transition-all duration-300 group ${cardClasses}">
                        <div class="flex items-center space-x-4">
                            <div class="p-2.5 rounded-xl transition-colors duration-300 ${iconContainerClasses}">
                                <i data-lucide="${topic.icon}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm transition-colors duration-300">${topic.name}</h4>
                                <p class="text-xs ${textMutedClasses} mt-0.5">${topic.count} ${topic.count === 1 ? 'article' : 'articles'}</p>
                            </div>
                        </div>
                        <i data-lucide="arrow-right" class="w-4 h-4 ${chevronClasses} group-hover:translate-x-1 transition-all duration-300"></i>
                    </div>
                `;
            }).join('');
            lucide.createIcons();
        }

        function handleTopicClick(topicId) {
            if (activeTopicId === topicId) {
                resetPortalHome();
                return;
            }

            activeTopicId = topicId;
            
            document.getElementById('portalHeroSection').classList.add('hidden');
            document.getElementById('portalPillarCards').classList.add('hidden');
            document.getElementById('portalArticlesView').classList.add('hidden');
            document.getElementById('portalFAQView').classList.add('hidden');
            document.getElementById('portalTermsView').classList.add('hidden');

            document.getElementById('portalPopularTopicsWrapper').classList.remove('hidden');
            document.getElementById('portalSatisfactionBanner').classList.remove('hidden');
            
            renderPopularTopicsGrid();
            renderFilteredArticles();
        }
        
        window.handleLiveSearch = function(query) {
            const dropdown = document.getElementById('searchSuggestionsDropdown');
            const cleanQuery = query.trim().toLowerCase();

            if (cleanQuery.length < 2) {
                dropdown.innerHTML = '';
                dropdown.classList.add('hidden');
                return;
            }

            let suggestionsHTML = '';

            const matchedArticles = articles.filter(art => 
                art.title.toLowerCase().includes(cleanQuery) || 
                art.desc.toLowerCase().includes(cleanQuery)
            );

            if (matchedArticles.length > 0) {
                suggestionsHTML += `<div class="px-3 py-1.5 bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Articles</div>`;
                matchedArticles.forEach(art => {
                    suggestionsHTML += `
                        <div onclick="selectSuggestion('ARTICLE', '${art.title.replace(/'/g, "\\'")}')" 
                             class="px-4 py-2.5 text-xs hover:bg-gray-50 cursor-pointer text-gray-700 font-medium flex items-center gap-2">
                             <i data-lucide="file-text" class="w-3.5 h-3.5 text-gray-400"></i>
                             <span class="truncate">${art.title}</span>
                        </div>`;
                });
            }

            const faqBlocks = document.querySelectorAll('#portalFAQView .space-y-1');
            let matchedFaqs = [];

            faqBlocks.forEach((node) => {
                const questionText = node.querySelector('h3')?.textContent || "";
                if (questionText.toLowerCase().includes(cleanQuery)) {
                    matchedFaqs.push({ title: questionText.replace('•', '').trim() });
                }
            });

            if (matchedFaqs.length > 0) {
                suggestionsHTML += `<div class="px-3 py-1.5 bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-wider">FAQs</div>`;
                matchedFaqs.forEach(faq => {
                    suggestionsHTML += `
                        <div onclick="selectSuggestion('FAQ', '${faq.title.replace(/'/g, "\\'")}')" 
                             class="px-4 py-2.5 text-xs hover:bg-gray-50 cursor-pointer text-gray-700 font-medium flex items-center gap-2">
                             <i data-lucide="help-circle" class="w-3.5 h-3.5 text-gray-400"></i>
                             <span class="truncate">${faq.title}</span>
                        </div>`;
                });
            }

            if (suggestionsHTML !== '') {
                dropdown.innerHTML = suggestionsHTML;
                dropdown.classList.remove('hidden');
                lucide.createIcons();
            } else {
                dropdown.innerHTML = `<div class="px-4 py-3 text-xs text-gray-400 italic font-medium">No matches found...</div>`;
                dropdown.classList.remove('hidden');
            }
        };

        window.selectSuggestion = function(type, title) {
            const input = document.getElementById('heroSearchInput');
            const dropdown = document.getElementById('searchSuggestionsDropdown');
            
            input.value = title;
            dropdown.classList.add('hidden');

            window.handleHeroSearch(title);
        };

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('searchSuggestionsDropdown');
            const input = document.getElementById('heroSearchInput');
            if (dropdown && !dropdown.contains(e.target) && e.target !== input) {
                dropdown.classList.add('hidden');
            }
        });

        window.handleHeroSearch = function(query) {
            const cleanQuery = query.trim().toLowerCase();
            if (!cleanQuery) return;

            const matchedArticle = articles.find(art => 
                art.title.toLowerCase().includes(cleanQuery) || 
                art.desc.toLowerCase().includes(cleanQuery) ||
                art.tags.some(t => t.toLowerCase().includes(cleanQuery))
            );

            if (matchedArticle) {
                switchView('ARTICLES');
                
                setTimeout(() => {
                    const articleNodes = document.querySelectorAll('#fullKnowledgeBaseStream h2');
                    let targetNode = null;
                    
                    articleNodes.forEach(node => {
                        if (node.textContent.includes(matchedArticle.title)) {
                            targetNode = node.closest('.bg-white');
                        }
                    });

                    if (targetNode) {
                        targetNode.style.transition = 'all 0.5s ease';
                        targetNode.style.outline = '3px solid #0f62fe';
                        targetNode.style.transform = 'scale(1.01)';
                        
                        targetNode.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        setTimeout(() => {
                            targetNode.style.outline = 'none';
                            targetNode.style.transform = 'none';
                        }, 3000);
                    }
                }, 150);
                return;
            }

            const faqBlocks = document.querySelectorAll('#portalFAQView .space-y-1');
            let matchedFaqNode = null;

            faqBlocks.forEach(node => {
                const questionText = node.querySelector('h3')?.textContent.toLowerCase() || "";
                const answerText = node.querySelector('p, div')?.textContent.toLowerCase() || "";
                
                if (questionText.includes(cleanQuery) || answerText.includes(cleanQuery)) {
                    matchedFaqNode = node;
                }
            });

            if (matchedFaqNode) {
                switchView('FAQ');
                
                setTimeout(() => {
                    matchedFaqNode.style.transition = 'all 0.5s ease';
                    matchedFaqNode.style.backgroundColor = '#eff6ff';
                    matchedFaqNode.style.padding = '0.5rem';
                    matchedFaqNode.style.borderRadius = '0.5rem';
                    
                    matchedFaqNode.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    setTimeout(() => {
                        matchedFaqNode.style.backgroundColor = 'transparent';
                    }, 3000);
                }, 150);
                return;
            }

            alert('No matching articles or FAQs found for your query.');
        };

        function renderFilteredArticles() {
            const container = document.getElementById('filteredArticlesContainer');
            const targetArticles = articles.filter(art => art.catId === activeTopicId);

            if (targetArticles.length === 0) {
                container.innerHTML = `<p class="text-xs text-gray-400 text-center py-6 font-medium">No articles listed here yet.</p>`;
                container.classList.remove('hidden');
                return;
            }

            container.innerHTML = targetArticles.map(art => generateArticleCardMarkup(art)).join('') + 
                `<div class="text-center py-4 text-xs text-gray-400 tracking-wide select-none font-medium">You've reached the end.</div>`;
            
            container.classList.remove('hidden');
            lucide.createIcons();
            targetArticles.forEach(art => trackArticleView(art.id));
        }

        function generateArticleCardMarkup(art) {
            const voted = votedArticleIds.get(art.id);
            const yesBtnClasses = voted === 'yes'
                ? 'bg-emerald-50 border-emerald-300 text-emerald-700'
                : 'bg-white border-gray-200 hover:bg-gray-50 text-gray-700';
            const noBtnClasses = voted === 'no'
                ? 'bg-rose-50 border-rose-300 text-rose-700'
                : 'bg-white border-gray-200 hover:bg-gray-50 text-gray-700';
            const disabledAttr = voted ? 'disabled' : '';

            return `
                <div id="articleCard-${art.id}" class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="p-8 space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">${art.title}</h2>
                        <p class="text-[14px] text-gray-500 leading-relaxed">
                            ${art.desc}
                            <span class="inline-flex flex-wrap gap-1.5 ml-1.5">
                                ${art.tags.map(t => `<span class="text-[11px] bg-gray-100 text-gray-400 px-1.5 py-0.5 rounded font-medium">#${t}</span>`).join('')}
                            </span>
                        </p>
                        <div class="flex items-center space-x-6 pt-1 text-xs text-gray-400 font-medium select-none">
                            <span class="bg-blue-50 text-[#0f62fe] font-semibold px-3 py-1 rounded-full text-[11px]">${art.category}</span>
                            <span class="flex items-center gap-1.5"><i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>${art.views} views</span>
                            <span class="flex items-center gap-1.5 text-emerald-500"><i data-lucide="thumbs-up" class="w-4 h-4"></i>${art.helpful} helpful</span>
                        </div>
                    </div>
                    <div class="bg-gray-50/80 border-t border-gray-100 p-5 px-8 space-y-3">
                        <span class="text-xs font-semibold text-gray-500 block">Was this article helpful?</span>
                        <div class="flex items-center space-x-3">
                            <button onclick="voteArticle(${art.id}, 'yes')" ${disabledAttr} class="inline-flex items-center space-x-2 border px-5 py-1.5 rounded-lg text-xs font-medium transition-all shadow-sm ${yesBtnClasses} ${voted ? 'cursor-not-allowed' : ''}">
                                <i data-lucide="thumbs-up" class="w-3.5 h-3.5 ${voted === 'yes' ? 'text-emerald-600' : 'text-emerald-500'}"></i><span>Yes (${art.yesVotes})</span>
                            </button>
                            <button onclick="voteArticle(${art.id}, 'no')" ${disabledAttr} class="inline-flex items-center space-x-2 border px-5 py-1.5 rounded-lg text-xs font-medium transition-all shadow-sm ${noBtnClasses} ${voted ? 'cursor-not-allowed' : ''}">
                                <i data-lucide="thumbs-down" class="w-3.5 h-3.5 ${voted === 'no' ? 'text-rose-600' : 'text-rose-400'}"></i><span>No (${art.noVotes})</span>
                            </button>
                        </div>
                        <span class="text-[11px] text-gray-400 block">${art.helpful} found this helpful${voted ? ' &middot; Thanks for your feedback!' : ''}</span>
                    </div>
                </div>
            `;
        }

        window.votedArticleIds = new Map();
        window.viewedArticleIds = new Set();

        function voteArticle(articleId, type) {
            if (votedArticleIds.has(articleId)) return;

            fetch(`/knowledge-base/${articleId}/vote`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ type })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    if (data.message) alert(data.message);
                    return;
                }
                votedArticleIds.set(articleId, type);

                const art = articles.find(a => a.id === articleId);
                if (art) {
                    art.yesVotes = data.yesVotes;
                    art.noVotes = data.noVotes;
                    art.helpful = data.helpful;
                }

                const cardEl = document.getElementById(`articleCard-${articleId}`);
                if (cardEl && art) {
                    cardEl.outerHTML = generateArticleCardMarkup(art);
                    lucide.createIcons();
                }
            })
            .catch(error => console.error("Error voting on article:", error));
        }

        function trackArticleView(articleId) {
            if (viewedArticleIds.has(articleId)) return;
            viewedArticleIds.add(articleId);

            fetch(`/knowledge-base/${articleId}/view`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;
                const art = articles.find(a => a.id === articleId);
                if (!art) return;
                art.views = (parseInt(String(art.views).replace(/,/g, '')) + 1).toLocaleString();
                const cardEl = document.getElementById(`articleCard-${articleId}`);
                if (cardEl) {
                    cardEl.outerHTML = generateArticleCardMarkup(art);
                    lucide.createIcons();
                }
            })
            .catch(error => console.error("Error tracking article view:", error));
        }

        function resetPortalHome() {
            activeTopicId = null;

            document.getElementById('portalHeroSection').classList.remove('hidden');
            document.getElementById('portalHeroSection').classList.add('opacity-100');
            document.getElementById('portalPillarCards').classList.remove('hidden');
            document.getElementById('portalPillarCards').classList.add('opacity-100');
            document.getElementById('portalPopularTopicsWrapper').classList.remove('hidden');
            document.getElementById('portalPopularTopicsWrapper').classList.add('opacity-100');
            document.getElementById('portalSatisfactionBanner').classList.remove('hidden');
            document.getElementById('portalSatisfactionBanner').classList.add('opacity-100');
            
            document.getElementById('filteredArticlesContainer').classList.add('hidden');
            document.getElementById('portalArticlesView').classList.add('hidden');
            document.getElementById('portalFAQView').classList.add('hidden');
            document.getElementById('portalTermsView').classList.add('hidden');

            renderPopularTopicsGrid();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function switchView(targetView) {
            activeTopicId = null; 

            document.getElementById('portalHeroSection').classList.add('hidden');
            document.getElementById('portalPillarCards').classList.add('hidden');
            document.getElementById('portalPopularTopicsWrapper').classList.add('hidden');
            document.getElementById('portalSatisfactionBanner').classList.add('hidden');
            document.getElementById('filteredArticlesContainer').classList.add('hidden');
            
            document.getElementById('portalArticlesView').classList.add('hidden');
            document.getElementById('portalFAQView').classList.add('hidden');
            document.getElementById('portalTermsView').classList.add('hidden');

            if (targetView === 'ARTICLES') {
                const stream = document.getElementById('fullKnowledgeBaseStream');
                
                if (articles.length === 0) {
                    stream.innerHTML = `<div class="col-span-2 text-center py-8 text-sm text-gray-400">No published articles found.</div>`;
                } else {
                    stream.innerHTML = articles.map(art => generateArticleCardMarkup(art)).join('') + 
                        `<div class="col-span-2 text-center py-4 text-xs text-gray-400 font-medium">You've reached the end.</div>`;
                    articles.forEach(art => trackArticleView(art.id));
                }
                
                document.getElementById('portalArticlesView').classList.remove('hidden');
                document.getElementById('portalSatisfactionBanner').classList.remove('hidden');
                document.getElementById('portalSatisfactionBanner').classList.add('opacity-100');
            } else if (targetView === 'FAQ') {
                document.getElementById('portalFAQView').classList.remove('hidden');
            } else if (targetView === 'TERMS') {
                document.getElementById('portalTermsView').classList.remove('hidden');
                document.getElementById('portalSatisfactionBanner').classList.remove('hidden');
                document.getElementById('portalSatisfactionBanner').classList.add('opacity-100');
            }

            lucide.createIcons();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Initialize App on Load
        renderPopularTopicsGrid();
        lucide.createIcons();
    </script>
</body>
</html>