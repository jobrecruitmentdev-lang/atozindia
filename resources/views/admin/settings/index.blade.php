@extends('layouts.admin')

@section('page_title', 'Store System Settings')
@section('page_subtitle', 'Configure taxes, logistics, currency, concierge contacts, and homepage announcement banners')

@section('content')
<div class="max-w-4xl space-y-8" x-data="{ section: 'general' }">
    
    <!-- Section Navigation -->
    <div class="bg-white rounded-2xl border border-neutral-200/80 p-1.5 shadow-sm flex flex-wrap gap-1">
        <button type="button" @click="section = 'general'" :class="section === 'general' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">1. General & Concierge</button>
        <button type="button" @click="section = 'financial'" :class="section === 'financial' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">2. Taxes & Currency</button>
        <button type="button" @click="section = 'shipping'" :class="section === 'shipping' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">3. Shipping & Delivery</button>
        <button type="button" @click="section = 'announcement'" :class="section === 'announcement' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">4. Announcement Strip</button>
        <button type="button" @click="section = 'payments'" :class="section === 'payments' ? 'bg-neutral-900 text-white font-bold' : 'text-neutral-600 hover:text-black'" class="px-4 py-2 rounded-xl text-xs transition-colors">5. Payment Gateways</button>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf

        <!-- 1. General & Concierge -->
        <div x-show="section === 'general'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Identity & Concierge Contacts</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Store Brand Name</label>
                    <input type="text" name="store_name" value="{{ $settings['store_name']->value ?? 'ATOZ GADGETZ' }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Brand Tagline / Slogan</label>
                    <input type="text" name="store_tagline" value="{{ $settings['store_tagline']->value ?? 'Architectural Tech & Haute Gadgetry' }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Concierge Support Email</label>
                    <input type="email" name="store_email" value="{{ $settings['store_email']->value ?? 'concierge@atozgadgetz.com' }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Concierge Phone Hotline</label>
                    <input type="text" name="store_phone" value="{{ $settings['store_phone']->value ?? '+91 (0) 124 498 7000' }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Store Headquarters Address</label>
                    <textarea name="store_address" rows="2" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">{{ $settings['store_address']->value ?? 'Skyview Towers, Tech Boulevard, Cyber City, Gurugram, India' }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Taxes & Currency -->
        <div x-show="section === 'financial'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Currency & Fiscal Policy</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Currency ISO Code</label>
                    <input type="text" name="currency" value="{{ $settings['currency']->value ?? 'INR' }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black uppercase">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Currency Display Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol']->value ?? '₹' }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Standard GST / Tax Rate (%)</label>
                    <input type="number" step="0.1" name="tax_rate" value="{{ $settings['tax_rate']->value ?? 18 }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Tax Display Mode</label>
                    <select name="tax_included_in_price" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                        <option value="1" {{ ($settings['tax_included_in_price']->value ?? '1') == '1' ? 'selected' : '' }}>Inclusive in prices (Standard Indian GST)</option>
                        <option value="0" {{ ($settings['tax_included_in_price']->value ?? '1') == '0' ? 'selected' : '' }}>Calculated at checkout</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Shipping & Delivery -->
        <div x-show="section === 'shipping'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Logistics & Free Shipping Threshold</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Complimentary Shipping Threshold (₹)</label>
                    <input type="number" step="0.01" name="free_shipping_threshold" value="{{ $settings['free_shipping_threshold']->value ?? 2999 }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black text-emerald-800 font-bold">
                    <p class="text-[10px] text-neutral-500 mt-1">Orders at or above this amount automatically receive complimentary white-glove delivery.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Standard Shipping Flat Fee (₹)</label>
                    <input type="number" step="0.01" name="standard_shipping_rate" value="{{ $settings['standard_shipping_rate']->value ?? 149 }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                    <p class="text-[10px] text-neutral-500 mt-1">Applied to orders below the complimentary threshold.</p>
                </div>
            </div>
        </div>

        <!-- 4. Announcement Strip -->
        <div x-show="section === 'announcement'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Top Announcement Marquee</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800 mb-4">
                        <input type="checkbox" name="announcement_enabled" value="1" {{ ($settings['announcement_enabled']->value ?? '1') == '1' ? 'checked' : '' }} class="rounded border-neutral-300 text-black">
                        <span>Display announcement banner above top navigation bar</span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Marquee Announcement Text</label>
                    <input type="text" name="announcement_text" value="{{ $settings['announcement_text']->value ?? 'Complimentary Express White-Glove Delivery on orders above ₹2,999' }}" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">Target Link</label>
                    <input type="text" name="announcement_link" value="{{ $settings['announcement_link']->value ?? '/shop' }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                </div>
            </div>
        </div>

        <!-- 5. Payment Gateways -->
        <div x-show="section === 'payments'" class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">Payment Methods & Limits</h3>
            
            <div class="space-y-4">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                    <input type="checkbox" name="payment_cod_enabled" value="1" {{ ($settings['payment_cod_enabled']->value ?? '1') == '1' ? 'checked' : '' }} class="rounded border-neutral-300 text-black">
                    <span>Enable Cash on Delivery (COD)</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">COD Minimum Order Value (₹)</label>
                        <input type="number" step="0.01" name="payment_cod_min_order" value="{{ $settings['payment_cod_min_order']->value ?? 499 }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-2">COD Maximum Order Value (₹)</label>
                        <input type="number" step="0.01" name="payment_cod_max_order" value="{{ $settings['payment_cod_max_order']->value ?? 50000 }}" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-2xl p-3 focus:outline-none focus:border-black">
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end">
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-all shadow-md">
                Save System Settings
            </button>
        </div>
    </form>
</div>
@endsection
