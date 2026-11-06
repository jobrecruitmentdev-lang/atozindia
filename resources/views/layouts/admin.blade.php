<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Concierge') — ATOZ GADGETZ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#F8F9FA] text-neutral-900 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        <!-- Sidebar Backdrop (Mobile) -->
        <div x-show="sidebarOpen" 
             x-cloak 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar Panel -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-[#FAF8F5] via-[#F4EDE0] to-[#EAE0CD] border-r border-[#D4AF37]/35 text-neutral-900 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Sidebar Header -->
            <div class="h-20 flex items-center px-6 border-b border-[#D4AF37]/20 gap-3">
                <img src="{{ asset('assets/images/logo.jpeg') }}" class="w-10 h-10 rounded-full object-cover border-2 border-neutral-950 shadow-md shrink-0" alt="ATOZ">
                <div class="flex flex-col">
                    <span class="font-black text-sm tracking-tight text-neutral-950 uppercase">ATOZ GADGETZ</span>
                    <span class="text-[9px] tracking-widest text-[#99772C] uppercase font-black">Concierge Studio</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6 text-xs font-medium">
                
                <!-- Overview -->
                <div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-[#99772C] px-3 mb-2 font-mono">Overview</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

                <!-- Catalogue -->
                <div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-[#99772C] px-3 mb-2 font-mono">Catalogue</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.products.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.products.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <span>Products</span>
                        </a>
                        <a href="{{ route('admin.categories.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.categories.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            <span>Categories</span>
                        </a>
                        <a href="{{ route('admin.brands.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.brands.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.brands.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span>Brands</span>
                        </a>
                    </div>
                </div>

                <!-- Inventory -->
                <div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-neutral-950 px-3 mb-2 font-mono">Inventory</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.inventory.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.inventory.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.inventory.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span>Stock Management</span>
                        </a>
                    </div>
                </div>

                <!-- Sales -->
                <div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-neutral-950 px-3 mb-2 font-mono">Sales</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.orders.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <span>Orders</span>
                        </a>
                        <a href="{{ route('admin.coupons.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.coupons.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.coupons.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            <span>Coupons</span>
                        </a>
                    </div>
                </div>

                <!-- Customers -->
                <div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-neutral-950 px-3 mb-2 font-mono">Customers</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.customers.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.customers.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span>Clientele</span>
                        </a>
                    </div>
                </div>

                <!-- Website & Content -->
                <div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-neutral-950 px-3 mb-2 font-mono">Storefront</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.banners.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.banners.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.banners.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Banners</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Configurations</span>
                        </a>
                    </div>
                </div>

                <!-- System -->
                <div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-neutral-950 px-3 mb-2 font-mono">Security</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.audit-logs.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.audit-logs.*') ? 'bg-neutral-950 text-[#DFBA53] font-black shadow-lg' : 'hover:bg-neutral-950/10 text-neutral-800 hover:text-neutral-950 font-bold' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.audit-logs.*') ? 'text-[#DFBA53]' : 'text-neutral-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Audit Trail</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Admin Profile / Logout Footer -->
            <div class="p-4 border-t border-[#D4AF37]/20 bg-black/5 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-neutral-950 text-[#DFBA53] flex items-center justify-center font-black text-xs shrink-0 shadow-sm border border-neutral-900">
                        {{ substr(auth('admin')->user()->name, 0, 1) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-black text-neutral-950 truncate">{{ auth('admin')->user()->name }}</p>
                        <p class="text-[10px] text-neutral-800 font-bold truncate">{{ auth('admin')->user()->is_super_admin ? 'Super Admin' : 'Staff' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 text-neutral-700 hover:text-black rounded-lg hover:bg-neutral-950/10 transition-colors" title="Sign Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Workspace -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            
            <!-- Top Bar -->
            <header class="h-20 bg-white border-b border-neutral-200/80 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-neutral-600 hover:text-[#99772C]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-neutral-900 tracking-tight">@yield('page_title', 'Admin Concierge')</h1>
                        <p class="text-xs text-neutral-500 hidden sm:block">@yield('page_subtitle', 'Live Store Management')</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-1.5 text-xs font-bold text-neutral-900 hover:text-neutral-950 bg-[#FDF9EE] hover:bg-[#D4AF37] border border-[#D4AF37]/40 px-3.5 py-2 rounded-full transition-colors shadow-sm">
                        <span>View Live Store</span>
                        <svg class="w-3.5 h-3.5 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
            </header>

            <!-- Alerts / Flashes -->
            <div class="px-6 sm:px-8 pt-6">
                @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium px-4 py-3 rounded-2xl flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if(session('error') || $errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium px-4 py-3 rounded-2xl space-y-1">
                    @if(session('error'))
                    <p>{{ session('error') }}</p>
                    @endif
                    @foreach($errors->all() as $err)
                    <p>&bull; {{ $err }}</p>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Page Body -->
            <main class="flex-1 px-6 sm:px-8 pb-12">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
