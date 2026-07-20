<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupportDesk - Knowledge Base Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:wght@100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Premium Portal Animations Setup (matching agent.blade) -->
    <style>
        body, * {
            font-family: 'Google Sans Flex', sans-serif !important;
        }

        @keyframes portalFadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes toastPop {
            0% { opacity: 0; transform: scale(0.92); }
            100% { opacity: 1; transform: scale(1); }
        }

        /* Force standard layout grids so transforms actually work on table rows */
        .table-grid {
            display: grid;
            grid-template-columns: 0.8fr 2fr 1.5fr 1fr 1fr 1.2fr 1fr;
            align-items: center;
            gap: 16px;
        }

        .ticket-row {
            opacity: 0;
            will-change: transform, opacity;
            transition:
                background-color 0.4s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.4s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Staggered entry animation exactly matching portal.blade */
        .animate-portal-reveal {
            animation: portalFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: calc(var(--row-index, 0) * 45ms);
        }

        /* Ultra-smooth floating lift effect */
        .ticket-row:hover {
            background-color: #f8fafc !important;
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08), 0 4px 12px -4px rgba(0, 0, 0, 0.04);
            border-color: #e2e8f0;
            z-index: 10;
        }

        /* Slim, rounded scrollbar to match the Tickets page */
        * {
            scrollbar-width:auto ;
            scrollbar-color: #828282 transparent;
        }
        *::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        *::-webkit-scrollbar-track {
            background: transparent;
        }
        *::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        *::-webkit-scrollbar-thumb:hover {
            background-color: #646464;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex overflow-hidden">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between fixed h-full z-30">
        <div>
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    SupportDesk
                </h1>
                <p class="text-xs text-gray-400 mt-1">E-commerce Support</p>
            </div>
            <nav class="p-4 space-y-1">
                <a href="{{ route('admin.support.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ route('agent') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="ticket" class="w-5 h-5"></i> Tickets
                </a>
                <a href="{{ route('KnowledgeBase') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg transition-all duration-300">
                    <i data-lucide="book-open" class="w-5 h-5 text-blue-600"></i> Knowledge Base
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition-all duration-300">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i> SLA Reports
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-gray-100">
            <a href="{{ route('CustomerPortal') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-purple-700 hover:bg-purple-50 rounded-lg transition-all duration-300">
                <i data-lucide="user" class="w-5 h-5"></i>
                Customer Portal
            </a>
        </div>
    </aside>

    <div class="flex-1 pl-64 flex flex-col h-screen overflow-hidden relative">
        
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-20 flex-shrink-0">
            <div class="relative w-96">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="headerSearch" oninput="handleSearch(this.value)" placeholder="Search tickets, customers, articles..." class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
            </div>
            <div class="flex items-center gap-4">

                <!-- Anchored Relative Parent Dropdown Holder -->
                <div class="relative">
                    <button id="notiToggle" class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-all focus:outline-none">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- FLOATING NOTIFICATIONS POPUP (Anchored right beneath the bell) -->
                    <div id="notiDropdown" class="hidden absolute right-0 mt-2 w-[360px] bg-white border border-gray-200 rounded-xl shadow-2xl z-50 flex flex-col overflow-hidden">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                            <button class="text-xs font-semibold text-blue-600 hover:underline">Mark all as read</button>
                        </div>
                        <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-100">

                            <!-- Notification 1: Matches TKT-1001 -->
                            <a href="{{ route('agent.ticket.details') }}" class="p-4 hover:bg-gray-50 transition-all flex items-start gap-3 relative block">
                                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-700 flex-shrink-0 flex items-center justify-center font-bold text-xs">CC</div>
                                <div class="flex-1 pr-3">
                                    <p class="text-xs font-bold text-gray-900">Overdue High Priority Ticket</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5"><span class="text-blue-600 font-semibold">#TKT-1001</span>: Order not received after 10 days by Charlize Casama.</p>
                                    <span class="text-[10px] text-gray-400 block mt-1">Just now</span>
                                </div>
                                <span class="w-2 h-2 bg-blue-600 rounded-full absolute right-4 top-1/2 -translate-y-1/2"></span>
                            </a>

                            <!-- Notification 2: Matches TKT-1002 -->
                            <a href="{{ route('agent.ticket.details') }}" class="p-4 hover:bg-gray-50 transition-all flex items-start gap-3 relative block">
                                <div class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 flex-shrink-0 flex items-center justify-center font-bold text-xs">GD</div>
                                <div class="flex-1 pr-3">
                                    <p class="text-xs font-bold text-gray-900">Overdue Medium Priority Ticket</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5"><span class="text-blue-600 font-semibold">#TKT-1002</span>: Received wrong item - ordered blue, got red by Gwen Dogelio.</p>
                                    <span class="text-[10px] text-gray-400 block mt-1">15 min ago</span>
                                </div>
                                <span class="w-2 h-2 bg-blue-600 rounded-full absolute right-4 top-1/2 -translate-y-1/2"></span>
                            </a>
                        </div>
                        <div class="p-3 bg-gray-50 border-t border-gray-100 text-center">
                            <a href="#" class="w-full inline-flex items-center justify-center gap-2 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                <i data-lucide="list" class="w-3.5 h-3.5"></i> View all notifications
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pl-2 border-l border-gray-200">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold text-xs">AD</div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 leading-tight">Admin User</p>
                        <p class="text-[10px] text-gray-400">Support Manager</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 flex-1 overflow-y-auto h-[calc(100vh-4rem)]">
            <div id="appWorkspaceContainer" class="max-w-7xl w-full mx-auto space-y-6">
            </div>
        </main>
    </div>

    <script>
    {{-- These comments tell your editor's linter what $articles/$categories are.
         Purely cosmetic - Laravel already injects them via compact() in the controller. --}}
    {{-- @var \Illuminate\Support\Collection $articles --}}
    {{-- @var \Illuminate\Support\Collection $categories --}}
    const articles = @json($articles);
    const categories = @json($categories);

    let currentView = 'LIST'; 
    let targetedCategoryId = "all";
    let activeArticleId = null;
    let isDropdownOpen = false;
    let selectedFormCategory = categories[1] ? categories[1].name : "Shipping & Delivery";
    
    // Track search input globally
    let searchQuery = "";

    const workspaceContainer = document.getElementById('appWorkspaceContainer');

        function renderWorkspace() {
            if (currentView === 'CREATE') {
                renderCreateFormLayout();
            } else if (currentView === 'EDIT') {
                renderEditFormLayout();
            } else {
                renderDashboardLayout();
            }
            lucide.createIcons();
        }

        function renderDashboardLayout() {
            // Total views: sum each article's view count (strings like "1,234" -> numbers)
            const totalViews = articles.reduce((sum, art) => sum + parseInt(String(art.views).replace(/,/g, ''), 10) || sum, 0);

            // Avg. helpfulness: aggregate yes/no votes across all articles that have any votes
            const votedArticles = articles.filter(art => (art.yesVotes + art.noVotes) > 0);
            const totalYes = votedArticles.reduce((sum, art) => sum + art.yesVotes, 0);
            const totalVotes = votedArticles.reduce((sum, art) => sum + art.yesVotes + art.noVotes, 0);
            const avgHelpfulness = totalVotes > 0 ? Math.round((totalYes / totalVotes) * 100) : 100;

            workspaceContainer.innerHTML = `
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Knowledge Base</h2>
                        <p class="text-sm text-gray-500 mt-1">Self-service portal for customers and support agents</p>
                    </div>
                    <button onclick="setView('CREATE')" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>New Article</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-4 space-y-6">
                        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                                </span>
                                <input type="text" id="sidebarSearch" oninput="handleSearch(this.value)" value="${searchQuery}" placeholder="Search articles..." class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                            <h3 class="text-sm font-bold text-gray-900 mb-3 tracking-wide">Categories</h3>
                            <ul id="categoryList" class="space-y-1"></ul>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                            <div class="flex items-center space-x-2.5 mb-4 pb-1">
                                <i data-lucide="trending-up" class="w-5 h-5 text-indigo-500"></i>
                                <h3 class="text-sm font-bold text-gray-900 tracking-wide">Most Viewed</h3>
                            </div>
                            <ul id="mostViewedList" class="space-y-4"></ul>
                        </div>
                    </div>

                    <div class="lg:col-span-8">
                        <div id="mainContentCard" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm min-h-[440px]"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 flex items-center justify-between shadow-sm">
                        <div><p class="text-xs font-semibold tracking-wider text-gray-400">Total Articles</p><h4 class="text-3xl font-bold text-gray-900 mt-2">${articles.length}</h4></div>
                        <div class="p-3 bg-blue-50 rounded-lg text-blue-600"><i data-lucide="book" class="w-6 h-6"></i></div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6 flex items-center justify-between shadow-sm">
                        <div><p class="text-xs font-semibold tracking-wider text-gray-400">Total Views</p><h4 class="text-3xl font-bold text-gray-900 mt-2">${totalViews.toLocaleString()}</h4></div>
                        <div class="p-3 bg-purple-50 rounded-lg text-purple-600"><i data-lucide="eye" class="w-6 h-6"></i></div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6 flex items-center justify-between shadow-sm">
                        <div><p class="text-xs font-semibold tracking-wider text-gray-400">Avg. Helpfulness</p><h4 class="text-3xl font-bold text-gray-900 mt-2">${avgHelpfulness}%</h4></div>
                        <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600"><i data-lucide="thumbs-up" class="w-6 h-6"></i></div>
                    </div>
                </div>
            `;

            renderCategoriesList();
            renderMostViewedWidget();
            
            if (currentView === 'DETAIL') {
                renderArticleDetailPane();
            } else {
                renderArticlesStreamPane();
            }
        }

        function renderCreateFormLayout() {
            workspaceContainer.innerHTML = `
                <div class="space-y-4">
                    <button onclick="setView('LIST')" class="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1 transition-all">
                        &larr; Back to Articles
                    </button>
                    
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Create New Article</h2>
                        <div class="text-xs text-gray-500">
                            Article Status : <span class="font-bold text-gray-700">Draft</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        <div class="lg:col-span-8 bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2 tracking-wide">Article Title *</label>
                                <input type="text" id="articleTitleInput" placeholder="Enter article title" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>

                            <div class="relative">
                                <label class="block text-xs font-bold text-gray-700 mb-2 tracking-wide">Category *</label>
                                <button onclick="toggleFormDropdown(event)" class="w-full border border-gray-200 bg-white rounded-lg p-2.5 text-sm text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                    <span class="text-gray-800">${selectedFormCategory}</span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                                </button>
                                
                                <div id="formCategoryDropdown" class="${isDropdownOpen ? 'block' : 'hidden'} absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-30 max-h-60 overflow-y-auto divide-y divide-gray-50">
                                    ${categories.filter(c => c.id !== 'all').map(cat => `
                                        <button onclick="selectFormCategory('${cat.name}', event)" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">${cat.name}</button>
                                    `).join('')}
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2 tracking-wide">Tags / Keywords *</label>
                                <input type="text" id="articleTagsInput" placeholder="e.g. delayed, order, issue" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2 tracking-wide">Body Content *</label>
                                <textarea id="articleBodyInput" rows="5" placeholder="Write the content description..." class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all resize-none"></textarea>
                            </div>

                            <div class="pt-1 flex items-center space-x-6">
                                <span class="text-sm font-medium text-gray-600">Visibility:</span>
                                <label class="inline-flex items-center space-x-2 cursor-pointer select-none">
                                    <input type="radio" name="visibility" value="public" checked class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Public</span>
                                </label>
                                <label class="inline-flex items-center space-x-2 cursor-pointer select-none">
                                    <input type="radio" name="visibility" value="internal" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Internal Only</span>
                                </label>
                            </div>
                        </div>

                        <div class="lg:col-span-4 bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4">
                            <div class="flex items-center space-x-2 text-indigo-600 border-b border-gray-100 pb-3">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                <h3 class="text-sm font-bold tracking-wide text-gray-900">Article Guide</h3>
                            </div>
                            <ul class="space-y-3 text-xs text-gray-600 leading-relaxed">
                                <li><strong class="text-gray-800 block mb-0.5">No Duplicates:</strong> Search existing articles to ensure this content is not already covered.</li>
                                <li><strong class="text-gray-800 block mb-0.5">Categorization:</strong> Place the article in the correct, most relevant category for user discovery.</li>
                                <li><strong class="text-gray-800 block mb-0.5">Accuracy:</strong> Verify all instructions, links, and examples are accurate and current.</li>
                                <li><strong class="text-gray-800 block mb-0.5">Precise Tagging:</strong> Use descriptive tags and keywords for search optimization.</li>
                                <li><strong class="text-gray-800 block mb-0.5">Sensitivity Check:</strong> Ensure no confidential support information or customer data is included.</li>
                            </ul>
                            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100">
                                <button onclick="handlePublishFormSubmit()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg text-xs transition-colors shadow-sm">Publish</button>
                                <button onclick="setView('LIST')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-lg text-xs transition-colors">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderEditFormLayout() {
            const article = articles.find(a => a.id === activeArticleId);
            if (!article) { setView('LIST'); return; }

            workspaceContainer.innerHTML = `
                <div class="space-y-4">
                    <button onclick="setArticleDetailView(${article.id})" class="text-xs font-semibold text-blue-600 hover:underline flex items-center gap-1 transition-all">
                        &larr; Back to Article
                    </button>
                    
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Article</h2>
                        <div class="text-xs text-gray-500">
                            Article Status : <span class="font-bold text-gray-700">${article.visibility === 'internal' ? 'Internal Only' : 'Public'}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        <div class="lg:col-span-8 bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2 tracking-wide">Article Title *</label>
                                <input type="text" id="editArticleTitleInput" value="${article.title.replace(/"/g, '&quot;')}" placeholder="Enter article title" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>

                            <div class="relative">
                                <label class="block text-xs font-bold text-gray-700 mb-2 tracking-wide">Category *</label>
                                <button onclick="toggleFormDropdown(event)" class="w-full border border-gray-200 bg-white rounded-lg p-2.5 text-sm text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                    <span class="text-gray-800">${selectedFormCategory}</span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                                </button>
                                
                                <div id="formCategoryDropdown" class="${isDropdownOpen ? 'block' : 'hidden'} absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-30 max-h-60 overflow-y-auto divide-y divide-gray-50">
                                    ${categories.filter(c => c.id !== 'all').map(cat => `
                                        <button onclick="selectFormCategory('${cat.name}', event)" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">${cat.name}</button>
                                    `).join('')}
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2 tracking-wide">Tags / Keywords *</label>
                                <input type="text" id="editArticleTagsInput" value="${article.tags.join(',')}" placeholder="e.g. delayed, order, issue" class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2 tracking-wide">Body Content *</label>
                                <textarea id="editArticleBodyInput" rows="5" placeholder="Write the content description..." class="w-full border border-gray-200 rounded-lg p-2.5 text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all resize-none">${article.desc}</textarea>
                            </div>

                            <div class="pt-1 flex items-center space-x-6">
                                <span class="text-sm font-medium text-gray-600">Visibility:</span>
                                <label class="inline-flex items-center space-x-2 cursor-pointer select-none">
                                    <input type="radio" name="editVisibility" value="public" ${article.visibility !== 'internal' ? 'checked' : ''} class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Public</span>
                                </label>
                                <label class="inline-flex items-center space-x-2 cursor-pointer select-none">
                                    <input type="radio" name="editVisibility" value="internal" ${article.visibility === 'internal' ? 'checked' : ''} class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Internal Only</span>
                                </label>
                            </div>
                        </div>

                        <div class="lg:col-span-4 bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4">
                            <div class="flex items-center space-x-2 text-indigo-600 border-b border-gray-100 pb-3">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                <h3 class="text-sm font-bold tracking-wide uppercase text-gray-900">Article Guide</h3>
                            </div>
                            <ul class="space-y-3.5 text-xs text-gray-600 leading-relaxed">
                                <li><strong class="text-gray-800 block mb-0.5">Visibility:</strong> Switch to Internal Only to hide this article from the customer portal while still keeping it visible to agents here.</li>
                                <li><strong class="text-gray-800 block mb-0.5">Categorization:</strong> Place the article in the correct category.</li>
                            </ul>
                            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100">
                                <button onclick="handleUpdateFormSubmit(${article.id})" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg text-xs transition-colors shadow-sm">Save Changes</button>
                                <button onclick="setArticleDetailView(${article.id})" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 rounded-lg text-xs transition-colors">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderCategoriesList() {
            const listEl = document.getElementById('categoryList');
            if (!listEl) return;
            listEl.innerHTML = categories.map(cat => {
                const isActive = cat.id === targetedCategoryId;
                const activeClasses = isActive ? "bg-blue-50 text-blue-600 font-semibold" : "text-gray-600 hover:bg-gray-50 font-medium";
                return `
                    <li>
                        <button onclick="setCategoryFilter('${cat.id}')" class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors text-left ${activeClasses}">
                            <span>${cat.name} (${cat.count})</span>
                        </button>
                    </li>
                `;
            }).join('');
        }

        function renderArticlesStreamPane() {
            const innerCard = document.getElementById('mainContentCard');
            if (!innerCard) return;

            // Step 1: Filter by Category
            let filtered = targetedCategoryId === "all" ? articles : articles.filter(a => a.catId === targetedCategoryId);
            
            // Step 2: Filter by Search Query
            if (searchQuery.trim() !== "") {
                filtered = filtered.filter(a => a.title.toLowerCase().includes(searchQuery.toLowerCase()));
            }

            const activeCatObj = categories.find(c => c.id === targetedCategoryId);

            innerCard.innerHTML = `
                <div class="border-b border-gray-100 pb-4 mb-4">
                    <h3 class="text-xl font-bold text-gray-900">${activeCatObj.name}</h3>
                    <p class="text-sm text-gray-400 mt-1">${filtered.length} article${filtered.length === 1 ? '' : 's'} found</p>
                </div>
                <div class="divide-y divide-gray-100">
                    ${filtered.length === 0 ? `
                        <div class="text-center py-12 text-gray-400 text-sm">No articles match your search criteria.</div>
                    ` : filtered.map(art => `
                        <article onclick="setArticleDetailView(${art.id})" class="py-5 first:pt-0 last:pb-0 group relative cursor-pointer select-none">
                            <div class="flex items-start justify-between">
                                <div class="space-y-2 max-w-2xl">
                                    <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">${art.title}</h4>
                                    <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">${art.desc}</p>
                                    <div class="flex flex-wrap items-center gap-4 pt-1 text-xs text-gray-400">
                                        <span class="bg-blue-50 text-blue-600 font-medium px-2 py-0.5 rounded text-[11px]">${art.category}</span>
                                        <span class="flex items-center gap-1"><i data-lucide="eye" class="w-3.5 h-3.5"></i> ${art.views} views</span>
                                        <span class="flex items-center gap-1"><i data-lucide="thumbs-up" class="w-3.5 h-3.5 text-emerald-500"></i> ${art.helpful} helpful</span>
                                    </div>
                                </div>
                                <button class="text-gray-300 group-hover:text-gray-500 p-1 transition-colors">
                                    <i data-lucide="book-open" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </article>
                    `).join('')}
                </div>
            `;
            lucide.createIcons();
        }

        function renderArticleDetailPane() {
            const innerCard = document.getElementById('mainContentCard');
            if (!innerCard) return;

            const article = articles.find(a => a.id === activeArticleId);
            if (!article) return;

            innerCard.innerHTML = `
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <button onclick="setView('LIST')" class="text-xs font-medium text-blue-600 hover:underline flex items-center gap-1 transition-all">&larr; Back to articles</button>
                        <button onclick="setEditView(${article.id})" class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-3 py-1.5 rounded-lg text-xs transition-colors">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            <span>Edit Article</span>
                        </button>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight leading-tight">${article.title}</h3>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 pb-4 border-b border-gray-200">
                        <span class="bg-blue-50 text-blue-600 font-semibold px-2.5 py-1 rounded-md text-[11px]">${article.category}</span>
                        <span class="${article.visibility === 'internal' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600'} font-semibold px-2.5 py-1 rounded-md text-[11px]">${article.visibility === 'internal' ? 'Internal Only' : 'Public'}</span>
                        <span class="flex items-center gap-1.5"><i data-lucide="eye" class="w-4 h-4 text-gray-400"></i> ${article.views} views</span>
                        <span>Updated ${article.updated}</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">${article.desc}</p>
                    <div class="flex flex-wrap items-center gap-2">
                        ${article.tags.map(tag => `<span class="text-[11px] font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded">#${tag}</span>`).join('')}
                    </div>
                </div>
            `;
            lucide.createIcons();
        }

        function renderMostViewedWidget() {
            const mostViewedListEl = document.getElementById('mostViewedList');
            if (!mostViewedListEl) return;
            const sorted = [...articles].sort((a, b) => parseInt(b.views.replace(',', '')) - parseInt(a.views.replace(',', ''))).slice(0, 5);
            
            mostViewedListEl.innerHTML = sorted.map(art => `
                <li class="flex flex-col space-y-0.5">
                    <button onclick="setArticleDetailView(${art.id})" class="text-xs font-semibold text-gray-700 hover:text-blue-600 text-left transition-colors line-clamp-1 w-full block">
                        ${art.title}
                    </button>
                    <span class="text-[11px] text-gray-400 flex items-center gap-1">
                        <i data-lucide="eye" class="w-3 h-3"></i> ${art.views} views
                    </span>
                </li>
            `).join('');
        }

        // Optimized Search: Updates text dynamically without breaking active keyboard focus
        window.handleSearch = function(val) {
            searchQuery = val;
            
            // Sync current values across elements smoothly without re-rendering their parent HTML
            const headerSearch = document.getElementById('headerSearch');
            if (headerSearch && headerSearch.value !== val) headerSearch.value = val;

            const sidebarSearch = document.getElementById('sidebarSearch');
            if (sidebarSearch && sidebarSearch.value !== val) sidebarSearch.value = val;

            // If the layout structure changes, run full render once, else just update stream container target
            if (currentView !== 'LIST') {
                currentView = 'LIST';
                activeArticleId = null;
                renderWorkspace();
            } else {
                renderArticlesStreamPane();
            }
        };

        window.setView = function(viewName) {
            currentView = viewName;
            if(viewName === 'LIST') activeArticleId = null;
            isDropdownOpen = false;
            renderWorkspace();
        };

        window.setCategoryFilter = function(catId) {
            targetedCategoryId = catId;
            currentView = 'LIST';
            activeArticleId = null;
            renderWorkspace();
        };

        window.setArticleDetailView = function(artId) {
            activeArticleId = artId;
            currentView = 'DETAIL';
            renderWorkspace();
        };

        window.setEditView = function(artId) {
            const article = articles.find(a => a.id === artId);
            if (!article) return;
            activeArticleId = artId;
            selectedFormCategory = article.category;
            isDropdownOpen = false;
            currentView = 'EDIT';
            renderWorkspace();
        };

        window.toggleFormDropdown = function(e) {
            e.stopPropagation();
            isDropdownOpen = !isDropdownOpen;
            const dropdown = document.getElementById('formCategoryDropdown');
            if(dropdown) {
                if(isDropdownOpen) dropdown.classList.remove('hidden');
                else dropdown.classList.add('hidden');
            }
        };

        window.selectFormCategory = function(catName, e) {
            e.stopPropagation();
            selectedFormCategory = catName;
            isDropdownOpen = false;
            renderWorkspace();
        };

        window.handlePublishFormSubmit = function() {
            const titleInput = document.getElementById('articleTitleInput').value;
            const tagsInput = document.getElementById('articleTagsInput').value;
            const descInput = document.getElementById('articleBodyInput').value;
            const visibilityInput = document.querySelector('input[name="visibility"]:checked').value;

            fetch("{{ route('kb.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    title: titleInput,
                    category: selectedFormCategory,
                    tags: tagsInput,
                    desc: descInput,
                    visibility: visibilityInput
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessToast();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => console.error("Error saving article:", error));
        };

        window.handleUpdateFormSubmit = function(articleId) {
            const titleInput = document.getElementById('editArticleTitleInput').value;
            const tagsInput = document.getElementById('editArticleTagsInput').value;
            const descInput = document.getElementById('editArticleBodyInput').value;
            const visibilityInput = document.querySelector('input[name="editVisibility"]:checked').value;

            fetch(`/knowledge-base/${articleId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    title: titleInput,
                    category: selectedFormCategory,
                    tags: tagsInput,
                    desc: descInput,
                    visibility: visibilityInput
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessToast('Article updated successfully!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => console.error("Error updating article:", error));
        };

        window.showSuccessToast = function(message) {
            const existing = document.getElementById('successToast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.id = 'successToast';
            toast.className = 'fixed inset-0 z-50 flex items-center justify-center pointer-events-none';
            toast.innerHTML = `
                <div class="pointer-events-auto bg-gradient-to-b from-white to-gray-200 rounded-2xl shadow-2xl px-10 py-8 flex flex-col items-center space-y-5 border border-gray-100" style="animation: toastPop 0.25s ease-out;">
                    <p class="text-lg font-bold text-gray-900 text-center tracking-tight">${message || 'Article Successfully Saved!'}</p>
                    <div class="w-14 h-14 rounded-full border-[2.5px] border-emerald-500 flex items-center justify-center">
                        <i data-lucide="check" class="w-7 h-7 text-emerald-500" stroke-width="3"></i>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => {
                toast.style.transition = 'opacity 0.4s ease';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        };

        window.onclick = function() {
            if (isDropdownOpen) {
                isDropdownOpen = false;
                const dropdown = document.getElementById('formCategoryDropdown');
                if(dropdown) dropdown.classList.add('hidden');
            }
        };

        // --- Notifications Open/Close Toggle Control (matching agent.blade) ---
        const notiToggleBtn = document.getElementById('notiToggle');
        const notiDropdownEl = document.getElementById('notiDropdown');

        if (notiToggleBtn && notiDropdownEl) {
            notiToggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notiDropdownEl.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!notiDropdownEl.contains(e.target) && e.target !== notiToggleBtn) {
                    notiDropdownEl.classList.add('hidden');
                }
            });
        }

        renderWorkspace();
    </script>
</body>
</html>