@extends('layouts.admin')

@section('page_title', 'Promotional Coupons')
@section('page_subtitle', 'Discount codes, percentage reductions, minimum cart thresholds, and redemption caps')

@section('content')
<div class="space-y-8">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Create Coupon Form -->
        <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm h-fit">
            <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3 mb-6">
                Create Promo Code
            </h3>

            <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Coupon Code *</label>
                    <input type="text" name="code" required placeholder="e.g. LUXURY25" class="w-full text-xs font-mono uppercase bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black font-bold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Discount Type *</label>
                        <select name="type" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Value *</label>
                        <input type="number" step="0.01" name="value" required placeholder="e.g. 15 or 1000" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Min Cart Spend (₹)</label>
                        <input type="number" step="0.01" name="min_spend" placeholder="e.g. 5000" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Max Discount (₹)</label>
                        <input type="number" step="0.01" name="max_discount" placeholder="For % coupons" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Total Usage Limit</label>
                    <input type="number" name="usage_limit_total" placeholder="Leave empty for unlimited" class="w-full text-xs font-mono bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-neutral-800">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-neutral-300 text-black">
                        <span>Active immediately</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">
                    Generate Coupon
                </button>
            </form>
        </div>

        <!-- Right: Coupons Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Active Promo Rules ({{ $coupons->total() }})</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">Automated discount validation rules applied during checkout.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                            <tr>
                                <th class="p-4">Code</th>
                                <th class="p-4">Benefit</th>
                                <th class="p-4">Conditions</th>
                                <th class="p-4">Redemptions</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse($coupons as $coupon)
                                <tr class="hover:bg-neutral-50/50 transition-colors">
                                    <td class="p-4">
                                        <span class="font-mono font-bold text-neutral-950 bg-neutral-100 px-2 py-1 rounded text-xs tracking-wider">
                                            {{ $coupon->code }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono font-bold text-neutral-900">
                                        @if($coupon->type === 'percentage')
                                            {{ $coupon->value }}% OFF
                                        @else
                                            ₹{{ number_format($coupon->value, 2) }} OFF
                                        @endif
                                    </td>
                                    <td class="p-4 text-neutral-500 text-[11px] space-y-0.5">
                                        @if($coupon->min_spend)
                                            <div>Min spend: ₹{{ number_format($coupon->min_spend, 2) }}</div>
                                        @endif
                                        @if($coupon->max_discount)
                                            <div>Max cap: ₹{{ number_format($coupon->max_discount, 2) }}</div>
                                        @endif
                                        @if(!$coupon->min_spend && !$coupon->max_discount)
                                            <span class="italic">No restrictions</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono text-neutral-700">
                                        {{ $coupon->redemptions_count }} uses
                                        @if($coupon->usage_limit_total)
                                            <span class="text-neutral-400">/ {{ $coupon->usage_limit_total }} max</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        @if($coupon->is_active)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-neutral-100 text-neutral-500">
                                                Disabled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Delete coupon {{ $coupon->code }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-neutral-400">No coupons active.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($coupons->hasPages())
                <div class="p-4 border-t border-neutral-100">
                    {{ $coupons->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
