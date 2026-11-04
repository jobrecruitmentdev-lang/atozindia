@extends('layouts.admin')

@section('page_title', 'Order #' . $order->order_number)
@section('page_subtitle', 'Placed on ' . $order->created_at->format('M d, Y \a\t H:i') . ' &bull; Customer: ' . $order->customer_name)

@section('content')
<div class="space-y-8">
    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-neutral-500 hover:text-black flex items-center gap-1">
            &larr; Back to All Orders
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl text-xs font-semibold border border-neutral-300 bg-white hover:bg-neutral-50 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Packing Slip</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Order Items & Invoicing -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Items Table Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Purchased Items ({{ $order->items->count() }})</h3>
                        <p class="text-xs text-neutral-500 mt-0.5">Immutable snapshot of item specifications at purchase time.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                            <tr>
                                <th class="p-4">Item & Variant</th>
                                <th class="p-4">SKU</th>
                                <th class="p-4">Price</th>
                                <th class="p-4">Qty</th>
                                <th class="p-4 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="p-4">
                                        <div class="font-bold text-neutral-900">{{ $item->product_name }}</div>
                                        @if($item->variant_title)
                                            <div class="text-[11px] text-neutral-500">{{ $item->variant_title }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono text-[11px] text-neutral-500 uppercase">
                                        {{ $item->sku }}
                                    </td>
                                    <td class="p-4 font-mono text-neutral-700">
                                        ₹{{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="p-4 font-mono font-bold text-neutral-900">
                                        &times; {{ $item->quantity }}
                                    </td>
                                    <td class="p-4 font-mono font-bold text-neutral-950 text-right">
                                        ₹{{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Financial Breakdown -->
                <div class="p-6 bg-neutral-50/50 border-t border-neutral-100 flex justify-end">
                    <div class="w-full sm:w-72 space-y-2 text-xs">
                        <div class="flex justify-between text-neutral-600">
                            <span>Subtotal</span>
                            <span class="font-mono">₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->discount_total > 0)
                            <div class="flex justify-between text-rose-600 font-medium">
                                <span>Discount ({{ $order->coupon_code }})</span>
                                <span class="font-mono">-₹{{ number_format($order->discount_total, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-neutral-600">
                            <span>Shipping</span>
                            <span class="font-mono">₹{{ number_format($order->shipping_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-neutral-600">
                            <span>Tax (18% GST incl.)</span>
                            <span class="font-mono">₹{{ number_format($order->tax_total, 2) }}</span>
                        </div>
                        <div class="border-t border-neutral-200 pt-3 flex justify-between font-bold text-sm text-neutral-950">
                            <span>Grand Total</span>
                            <span class="font-mono text-base">₹{{ number_format($order->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status History Audit Trail -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 sm:p-8 shadow-sm space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">
                    Lifecycle & Status History
                </h3>

                <div class="relative pl-6 border-l-2 border-neutral-200 space-y-6">
                    @forelse($order->statusHistories as $h)
                        <div class="relative">
                            <span class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-neutral-900 border-2 border-white ring-2 ring-neutral-200"></span>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-neutral-900 uppercase tracking-wide">{{ $h->status_type }}: {{ $h->to_status }}</span>
                                <span class="text-neutral-400 font-mono">{{ $h->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="text-xs text-neutral-600 mt-1">{{ $h->comment }}</div>
                            @if($h->admin)
                                <div class="text-[10px] text-neutral-400 mt-0.5">Updated by {{ $h->admin->name }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-neutral-400">No status updates logged yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Customer Details & Status Control -->
        <div class="space-y-8">
            
            <!-- Status Update Controller -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">
                    Fulfillment Workflow
                </h3>

                <form method="POST" action="{{ route('admin.orders.status.update', $order->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Action Target</label>
                        <select name="status_type" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                            <option value="order_status">Order Status (Current: {{ $order->order_status }})</option>
                            <option value="payment_status">Payment Status (Current: {{ $order->payment_status }})</option>
                            <option value="fulfillment_status">Fulfillment Status (Current: {{ $order->fulfillment_status }})</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Transition To</label>
                        <select name="new_status" class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                            <option value="pending">pending</option>
                            <option value="processing">processing</option>
                            <option value="completed">completed</option>
                            <option value="cancelled">cancelled (Restores Stock)</option>
                            <option value="paid">paid</option>
                            <option value="shipped">shipped</option>
                            <option value="delivered">delivered</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Internal Audit Note</label>
                        <input type="text" name="comment" placeholder="Reason for transition..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-xl p-2.5 focus:outline-none focus:border-black">
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors shadow">
                        Record Status Update
                    </button>
                </form>
            </div>

            <!-- Customer & Shipping Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm space-y-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-100 pb-3">
                    Customer Information
                </h3>

                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Client</span>
                        <span class="font-bold text-neutral-900">{{ $order->customer_name }}</span>
                    </div>

                    <div>
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Email</span>
                        <a href="mailto:{{ $order->customer_email }}" class="text-neutral-700 hover:text-black underline font-mono">{{ $order->customer_email }}</a>
                    </div>

                    <div>
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Phone</span>
                        <span class="font-mono text-neutral-800">{{ $order->customer_phone ?: 'None provided' }}</span>
                    </div>

                    <div class="border-t border-neutral-100 pt-4">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Shipping Destination</span>
                        @if($order->shippingAddress)
                            <div class="text-neutral-700 leading-relaxed">
                                {{ $order->shippingAddress->address_line1 }}<br>
                                @if($order->shippingAddress->address_line2) {{ $order->shippingAddress->address_line2 }}<br> @endif
                                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
                                {{ $order->shippingAddress->country }}
                            </div>
                        @else
                            <span class="text-neutral-400 italic">No physical address attached</span>
                        @endif
                    </div>

                    <div class="border-t border-neutral-100 pt-4">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Payment Method</span>
                        <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-mono bg-neutral-100 text-neutral-800 uppercase font-semibold">
                            {{ $order->payment_method }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
