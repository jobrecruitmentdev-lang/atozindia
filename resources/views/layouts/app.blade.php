<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ATOZ GADGETZ — Architectural Tech & Haute Gadgetry')</title>
    <meta name="description" content="@yield('meta_description', 'Discover luxury electronics, studio-grade acoustics, and precision desk architecture.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#FBFBFA] text-neutral-900 font-sans antialiased selection:bg-neutral-900 selection:text-white"
      x-data="globalApp()"
      @cart-updated.window="fetchCartDrawer()"
      @toast.window="addToast($event.detail.message, $event.detail.type || 'success')">

    <!-- Announcement Bar -->
    @if(\App\Models\StoreSetting::get('announcement_enabled', true))
    <div class="bg-neutral-900 text-white text-[11px] sm:text-xs font-medium py-2 px-4 text-center tracking-wide uppercase transition-all flex items-center justify-center gap-2">
        <span>{{ \App\Models\StoreSetting::get('announcement_text', 'Complimentary Express White-Glove Delivery on orders above ₹2,999') }}</span>
        @if(\App\Models\StoreSetting::get('announcement_link'))
        <a href="{{ \App\Models\StoreSetting::get('announcement_link') }}" class="underline hover:text-neutral-300 ml-1 font-semibold">Shop Now &rarr;</a>
        @endif
    </div>
    @endif

    <!-- Premium Sticky Header -->
    <header class="sticky top-0 z-40 w-full transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-neutral-200/80"
            :class="{ 'shadow-sm': scrolled }"
            @scroll.window="scrolled = (window.pageYOffset > 20)">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            
            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    type="button" 
                    class="lg:hidden p-2 text-neutral-700 hover:text-black focus:outline-none" 
                    aria-label="Open menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none shrink-0">
                <img src="{{ asset('assets/images/logo.jpeg') }}" 
                     alt="ATOZ GADGETZ" 
                     class="h-11 w-11 rounded-full object-cover border border-neutral-300/80 shadow-sm transition-transform duration-300 group-hover:scale-105">
                <div class="flex flex-col">
                    <span class="font-extrabold text-lg sm:text-xl tracking-tight text-neutral-950 font-sans uppercase">ATOZ GADGETZ</span>
                    <span class="text-[9px] tracking-[0.2em] font-semibold text-neutral-500 uppercase -mt-1">Architectural Tech</span>
                </div>
            </a>

            <!-- Navigation Links / Mega Menu (Desktop) -->
            <nav class="hidden lg:flex items-center gap-8 text-[13px] font-semibold tracking-wide uppercase">
                <a href="{{ route('home') }}" class="text-neutral-900 hover:text-neutral-500 transition-colors {{ request()->routeIs('home') ? 'border-b-2 border-black pb-1' : '' }}">Home</a>
                <a href="{{ route('shop.index') }}" class="text-neutral-900 hover:text-neutral-500 transition-colors {{ request()->routeIs('shop.index') && !request('category') ? 'border-b-2 border-black pb-1' : '' }}">All Products</a>
                <a href="{{ route('shop.index', ['category' => 'audio-acoustics']) }}" class="text-neutral-900 hover:text-neutral-500 transition-colors">Audio</a>
                <a href="{{ route('shop.index', ['category' => 'wearables']) }}" class="text-neutral-900 hover:text-neutral-500 transition-colors">Wearables</a>
                <a href="{{ route('shop.index', ['category' => 'workspace-architecture']) }}" class="text-neutral-900 hover:text-neutral-500 transition-colors">Desk</a>
                <a href="{{ route('shop.index', ['category' => 'optics-cameras']) }}" class="text-neutral-900 hover:text-neutral-500 transition-colors">Optics</a>
            </nav>

            <!-- Smart Search Bar (Desktop & Tablet) -->
            <div class="relative hidden md:block flex-1 max-w-xs lg:max-w-sm" x-data="searchAutocomplete()">
                <div class="relative">
                    <input type="text" 
                           x-model="query" 
                           @input.debounce.250ms="fetchSuggestions()" 
                           @focus="open = true" 
                           @click.outside="open = false"
                           placeholder="Search gadgets, audio, titanium..." 
                           class="w-full bg-neutral-100/80 hover:bg-neutral-100 text-neutral-900 text-xs rounded-full pl-9 pr-4 py-2.5 border border-transparent focus:border-neutral-400 focus:bg-white focus:outline-none transition-all placeholder:text-neutral-400">
                    <svg class="w-4 h-4 text-neutral-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Autocomplete Dropdown -->
                <div x-show="open && (results.products.length > 0 || results.categories.length > 0 || results.brands.length > 0)"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-cloak
                     class="absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl shadow-xl border border-neutral-200/90 overflow-hidden z-50 p-3">
                    
                    <!-- Products -->
                    <template x-if="results.products.length > 0">
                        <div>
                            <div class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-2 px-2">Products</div>
                            <div class="space-y-1">
                                <template x-for="item in results.products" :key="item.id">
                                    <a :href="item.url" class="flex items-center gap-3 p-2 rounded-xl hover:bg-neutral-50 transition-colors">
                                        <img :src="item.image" class="w-10 h-10 object-cover rounded-lg bg-neutral-100 shrink-0" :alt="item.name">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-semibold text-neutral-900 truncate" x-text="item.name"></div>
                                            <div class="text-[11px] text-neutral-500" x-text="item.brand"></div>
                                        </div>
                                        <div class="text-xs font-bold text-neutral-900" x-text="item.price"></div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Categories & Brands -->
                    <template x-if="results.categories.length > 0 || results.brands.length > 0">
                        <div class="border-t border-neutral-100 mt-2 pt-2 grid grid-cols-2 gap-2 text-xs">
                            <template x-for="cat in results.categories" :key="cat.name">
                                <a :href="cat.url" class="p-1.5 text-neutral-600 hover:text-black font-medium hover:bg-neutral-50 rounded-lg truncate">
                                    &rarr; Category: <span class="font-semibold text-neutral-900" x-text="cat.name"></span>
                                </a>
                            </template>
                            <template x-for="br in results.brands" :key="br.name">
                                <a :href="br.url" class="p-1.5 text-neutral-600 hover:text-black font-medium hover:bg-neutral-50 rounded-lg truncate">
                                    &rarr; Brand: <span class="font-semibold text-neutral-900" x-text="br.name"></span>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Header Action Icons -->
            <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                <!-- User Account Dropdown -->
                <div class="relative" x-data="{ userMenu: false }">
                    @auth('web')
                    <button @click="userMenu = !userMenu" @click.outside="userMenu = false" class="flex items-center gap-2 p-1.5 rounded-full hover:bg-neutral-100 text-neutral-800 transition-colors focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center text-xs font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="hidden sm:inline text-xs font-semibold max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                    </button>
                    <div x-show="userMenu" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-neutral-200/90 py-2 z-50 text-xs">
                        <div class="px-4 py-2 border-b border-neutral-100">
                            <p class="font-semibold text-neutral-900">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-neutral-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 hover:bg-neutral-50 text-neutral-700">Account Dashboard</a>
                        <a href="{{ route('customer.orders') }}" class="block px-4 py-2 hover:bg-neutral-50 text-neutral-700">My Orders</a>
                        <a href="{{ route('customer.wishlist') }}" class="block px-4 py-2 hover:bg-neutral-50 text-neutral-700">Saved Wishlist</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-neutral-100 mt-1">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 font-medium">Sign Out</button>
                        </form>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="flex items-center gap-1.5 p-2 rounded-full hover:bg-neutral-100 text-neutral-800 transition-colors" title="Account Login">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="hidden sm:inline text-xs font-semibold">Sign In</span>
                    </a>
                    @endauth
                </div>

                <!-- Wishlist Icon -->
                @auth('web')
                <a href="{{ route('customer.wishlist') }}" class="relative p-2 rounded-full hover:bg-neutral-100 text-neutral-800 transition-colors" title="Wishlist">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    @if(auth()->user()->wishlist && auth()->user()->wishlist->items()->count() > 0)
                    <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-rose-600"></span>
                    @endif
                </a>
                @endauth

                <!-- Cart Drawer Trigger Button -->
                <button @click="cartDrawerOpen = true; fetchCartDrawer()" 
                        type="button" 
                        class="flex items-center gap-2 bg-neutral-900 hover:bg-black text-white px-4 py-2.5 rounded-full text-xs font-semibold shadow-sm transition-transform active:scale-95 focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span class="hidden sm:inline">Bag</span>
                    <span class="bg-white/20 text-white rounded-full px-1.5 py-0.2 text-[11px] font-bold" x-text="cart.items_count">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Slide-Out Cart Drawer (Alpine.js) -->
    <div x-show="cartDrawerOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-hidden" 
         aria-labelledby="slide-over-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="cartDrawerOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="cartDrawerOpen = false"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
            <div x-show="cartDrawerOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
                
                <!-- Drawer Header -->
                <div class="px-6 py-5 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-neutral-950 uppercase tracking-tight">Shopping Bag</h2>
                        <p class="text-xs text-neutral-500" x-text="cart.items_count + ' curated item(s)'"></p>
                    </div>
                    <button @click="cartDrawerOpen = false" class="p-2 text-neutral-400 hover:text-black rounded-full hover:bg-neutral-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Free Shipping Tier Bar -->
                <div class="px-6 py-3 bg-neutral-50 border-b border-neutral-100 text-xs text-neutral-600">
                    <template x-if="cart.remaining_for_free_shipping > 0">
                        <div>
                            Add <span class="font-bold text-neutral-900" x-text="'₹' + Number(cart.remaining_for_free_shipping).toFixed(2)"></span> more for <strong>Free Express Shipping</strong>
                            <div class="w-full bg-neutral-200 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="bg-neutral-900 h-full transition-all duration-300" 
                                     :style="'width: ' + Math.min(100, ((cart.free_shipping_threshold - cart.remaining_for_free_shipping) / cart.free_shipping_threshold) * 100) + '%'"></div>
                            </div>
                        </div>
                    </template>
                    <template x-if="cart.remaining_for_free_shipping <= 0">
                        <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            You have unlocked Complimentary White-Glove Shipping!
                        </div>
                    </template>
                </div>

                <!-- Cart Items List -->
                <div class="flex-1 overflow-y-auto px-6 py-4 divide-y divide-neutral-100">
                    <template x-if="cart.items.length === 0">
                        <div class="py-16 text-center text-neutral-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <p class="text-sm font-medium text-neutral-700">Your bag is currently empty.</p>
                            <a href="{{ route('shop.index') }}" @click="cartDrawerOpen = false" class="inline-block mt-4 text-xs font-bold uppercase tracking-wider text-black border-b border-black pb-0.5">Explore Catalog &rarr;</a>
                        </div>
                    </template>

                    <template x-for="item in cart.items" :key="item.id">
                        <div class="py-4 flex gap-4 items-center">
                            <img :src="item.thumbnail" class="w-16 h-16 object-cover rounded-xl bg-neutral-100 border border-neutral-100 shrink-0" :alt="item.name">
                            <div class="flex-1 min-w-0">
                                <a :href="'/product/' + item.slug" class="text-xs font-bold text-neutral-900 hover:text-neutral-600 truncate block" x-text="item.name"></a>
                                <p class="text-[11px] text-neutral-500 mt-0.5" x-text="item.variant_summary || item.sku"></p>
                                <div class="text-xs font-bold text-neutral-900 mt-1" x-text="item.total_price"></div>
                                
                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="flex items-center border border-neutral-200 rounded-full overflow-hidden text-xs">
                                        <button @click="updateCartItem(item.id, item.quantity - 1)" class="px-2.5 py-1 text-neutral-600 hover:bg-neutral-100">-</button>
                                        <span class="px-2 font-semibold text-neutral-900" x-text="item.quantity"></span>
                                        <button @click="updateCartItem(item.id, item.quantity + 1)" class="px-2.5 py-1 text-neutral-600 hover:bg-neutral-100">+</button>
                                    </div>
                                    <button @click="removeCartItem(item.id)" class="text-[11px] text-neutral-400 hover:text-rose-600 underline ml-2">Remove</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Drawer Sticky Footer -->
                <template x-if="cart.items.length > 0">
                    <div class="p-6 border-t border-neutral-100 bg-neutral-50/50 space-y-3">
                        <!-- Coupon Form -->
                        <div class="flex gap-2">
                            <input type="text" 
                                   x-model="couponCodeInput" 
                                   placeholder="Coupon / Gift Code" 
                                   class="flex-1 text-xs uppercase px-3 py-2 rounded-xl border border-neutral-200 bg-white focus:outline-none focus:border-black">
                            <button @click="applyCouponCode()" 
                                    class="bg-neutral-200 hover:bg-neutral-300 text-neutral-800 px-3 py-2 rounded-xl text-xs font-semibold">Apply</button>
                        </div>

                        <!-- Calculations -->
                        <div class="space-y-1.5 text-xs text-neutral-600 pt-2 border-t border-neutral-200/60">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="font-semibold text-neutral-900" x-text="cart.subtotal"></span>
                            </div>
                            <template x-if="cart.discount !== '₹0.00'">
                                <div class="flex justify-between text-emerald-600 font-medium">
                                    <span>Discount (<span x-text="cart.coupon_code"></span>)</span>
                                    <span x-text="'-' + cart.discount"></span>
                                </div>
                            </template>
                            <div class="flex justify-between">
                                <span>Shipping</span>
                                <span class="font-semibold text-neutral-900" x-text="cart.shipping"></span>
                            </div>
                            <div class="flex justify-between text-sm font-bold text-neutral-950 pt-2 border-t border-neutral-200">
                                <span>Grand Total</span>
                                <span x-text="cart.total"></span>
                            </div>
                        </div>

                        <a href="{{ route('checkout.index') }}" 
                           class="w-full bg-neutral-950 hover:bg-black text-white text-xs font-bold uppercase tracking-wider py-3.5 rounded-full text-center flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-all">
                            <span>Proceed to Checkout</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Main Dynamic Content -->
    <main>
        @yield('content')
    </main>

    <!-- Premium Luxury Footer -->
    <footer class="bg-neutral-950 text-white mt-24 border-t border-neutral-900">
        <!-- Global Trust / Guarantee Bar -->
        <div class="border-b border-neutral-900/80">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 text-center sm:text-left">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-neutral-900 flex items-center justify-center shrink-0 text-white border border-neutral-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Genuine Craftsmanship</h4>
                        <p class="text-xs text-neutral-400 mt-0.5">100% certified authentic luxury electronics.</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-neutral-900 flex items-center justify-center shrink-0 text-white border border-neutral-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Express White-Glove</h4>
                        <p class="text-xs text-neutral-400 mt-0.5">Insured air transit on all premium orders.</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-neutral-900 flex items-center justify-center shrink-0 text-white border border-neutral-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Global 2-Year Warranty</h4>
                        <p class="text-xs text-neutral-400 mt-0.5">Comprehensive hardware protection & repair.</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-neutral-900 flex items-center justify-center shrink-0 text-white border border-neutral-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Concierge Support</h4>
                        <p class="text-xs text-neutral-400 mt-0.5">24/7 dedicated acoustic & gadget advisors.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Columns -->
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 text-xs">
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/logo.jpeg') }}" class="w-9 h-9 rounded-full object-cover border border-neutral-800" alt="ATOZ GADGETZ">
                    <span class="font-extrabold text-lg tracking-tight uppercase">ATOZ GADGETZ</span>
                </div>
                <p class="text-neutral-400 text-xs leading-relaxed max-w-sm">
                    Curating high-performance acoustic instruments, luxury biometric wearables, and architectural desk hardware. Designed for purists of material and form.
                </p>
                <div class="pt-2">
                    <p class="text-[11px] text-neutral-500 uppercase tracking-wider font-semibold">Join the Private Society</p>
                    <div class="flex gap-2 mt-2 max-w-sm">
                        <input type="email" placeholder="Enter your email address" class="bg-neutral-900 border border-neutral-800 rounded-full px-4 py-2.5 text-xs text-white focus:outline-none focus:border-white flex-1 placeholder:text-neutral-500">
                        <button type="button" class="bg-white text-black font-bold uppercase px-4 py-2.5 rounded-full text-xs hover:bg-neutral-200">Join</button>
                    </div>
                </div>
            </div>

            <div>
                <h5 class="font-bold uppercase tracking-wider text-white mb-4">Curations</h5>
                <ul class="space-y-2.5 text-neutral-400">
                    <li><a href="{{ route('shop.index', ['category' => 'audio-acoustics']) }}" class="hover:text-white transition-colors">Over-Ear Studio</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'wearables']) }}" class="hover:text-white transition-colors">Titanium Timepieces</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'workspace-architecture']) }}" class="hover:text-white transition-colors">Desk Architecture</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'optics-cameras']) }}" class="hover:text-white transition-colors">Rangefinder Optics</a></li>
                    <li><a href="{{ route('shop.index', ['sort' => 'newest']) }}" class="hover:text-white transition-colors">New Releases</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-bold uppercase tracking-wider text-white mb-4">Concierge</h5>
                <ul class="space-y-2.5 text-neutral-400">
                    <li><a href="#" class="hover:text-white transition-colors">Order Tracking</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">White-Glove Delivery</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Concierge Returns</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Warranty & Care</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Acoustic Consultation</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-bold uppercase tracking-wider text-white mb-4">Society</h5>
                <ul class="space-y-2.5 text-neutral-400">
                    <li><a href="#" class="hover:text-white transition-colors">Our Design Philosophy</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Material Manifesto</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Sustainability Standards</a></li>
                    <li><a href="{{ route('admin.login') }}" class="hover:text-white transition-colors text-neutral-500">Staff Portal</a></li>
                </ul>
            </div>
        </div>

        <!-- Copyright & Legal -->
        <div class="border-t border-neutral-900 text-neutral-500 text-[11px] py-6">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p>&copy; {{ date('Y') }} ATOZ GADGETZ. All architectural rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-white">Privacy Protocol</a>
                    <a href="#" class="hover:text-white">Terms of Curatorship</a>
                    <a href="#" class="hover:text-white">Security Disclosures</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Toast Container -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 max-w-sm" x-cloak>
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-neutral-900 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-xs border border-neutral-800">
                <span class="w-2 h-2 rounded-full" :class="toast.type === 'error' ? 'bg-rose-500' : 'bg-emerald-400'"></span>
                <span class="flex-1 font-medium" x-text="toast.message"></span>
                <button @click="toast.visible = false" class="text-neutral-400 hover:text-white">&times;</button>
            </div>
        </template>
    </div>

    <!-- Global Alpine Store & Scripts -->
    <script>
        function globalApp() {
            return {
                scrolled: false,
                mobileMenuOpen: false,
                cartDrawerOpen: false,
                couponCodeInput: '',
                cart: {
                    items: [],
                    items_count: 0,
                    subtotal: '₹0.00',
                    discount: '₹0.00',
                    shipping: 'FREE',
                    tax: '₹0.00',
                    total: '₹0.00',
                    remaining_for_free_shipping: 2999,
                    free_shipping_threshold: 2999,
                    coupon_code: null
                },
                toasts: [],
                init() {
                    this.fetchCartDrawer();
                },
                fetchCartDrawer() {
                    fetch('{{ route("cart.drawer") }}')
                        .then(r => r.json())
                        .then(d => {
                            this.cart = d;
                        })
                        .catch(e => console.error(e));
                },
                addToCart(productId, variantId = null, qty = 1) {
                    fetch('{{ route("cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            variant_id: variantId,
                            quantity: qty
                        })
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            this.fetchCartDrawer();
                            this.cartDrawerOpen = true;
                            this.addToast(d.message || 'Item added to your shopping bag.');
                        } else {
                            this.addToast(d.message || 'Could not add item to bag.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        this.addToast('An error occurred adding to bag.', 'error');
                    });
                },
                updateCartItem(id, qty) {
                    fetch('/cart/update/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ quantity: qty })
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            this.cart = d.cart;
                        } else {
                            this.addToast(d.message, 'error');
                        }
                    });
                },
                removeCartItem(id) {
                    fetch('/cart/remove/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        this.cart = d.cart;
                        this.addToast('Item removed from your bag.');
                    });
                },
                applyCouponCode() {
                    if (!this.couponCodeInput.trim()) return;
                    fetch('{{ route("cart.coupon.apply") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ code: this.couponCodeInput })
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            this.cart = d.cart;
                            this.couponCodeInput = '';
                            this.addToast(d.message);
                        } else {
                            this.addToast(d.message, 'error');
                        }
                    });
                },
                addToast(msg, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, message: msg, type, visible: true });
                    setTimeout(() => {
                        const t = this.toasts.find(x => x.id === id);
                        if (t) t.visible = false;
                    }, 4000);
                }
            };
        }

        function searchAutocomplete() {
            return {
                query: '',
                open: false,
                results: { products: [], categories: [], brands: [] },
                fetchSuggestions() {
                    if (this.query.length < 2) {
                        this.results = { products: [], categories: [], brands: [] };
                        return;
                    }
                    fetch('{{ route("search.suggestions") }}?q=' + encodeURIComponent(this.query))
                        .then(r => r.json())
                        .then(data => {
                            this.results = data;
                            this.open = true;
                        });
                }
            };
        }
    </script>

    @stack('scripts')
</body>
</html>
