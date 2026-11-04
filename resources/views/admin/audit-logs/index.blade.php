@extends('layouts.admin')

@section('page_title', 'System Audit Trail & Security Logs')
@section('page_subtitle', 'Forensic ledger recording every catalogue change, price modification, and order state transition')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Bar -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <select name="action" class="text-xs bg-neutral-50 border border-neutral-200 rounded-2xl px-4 py-2.5 focus:outline-none focus:border-black">
                    <option value="">All Actions</option>
                    <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>created</option>
                    <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>updated</option>
                    <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>deleted</option>
                    <option value="status_updated" {{ request('action') === 'status_updated' ? 'selected' : '' }}>status_updated</option>
                    <option value="stock_adjusted" {{ request('action') === 'stock_adjusted' ? 'selected' : '' }}>stock_adjusted</option>
                </select>

                <select name="entity_type" class="text-xs bg-neutral-50 border border-neutral-200 rounded-2xl px-4 py-2.5 focus:outline-none focus:border-black">
                    <option value="">All Entities</option>
                    <option value="Product" {{ request('entity_type') === 'Product' ? 'selected' : '' }}>Product</option>
                    <option value="Order" {{ request('entity_type') === 'Order' ? 'selected' : '' }}>Order</option>
                    <option value="Inventory" {{ request('entity_type') === 'Inventory' ? 'selected' : '' }}>Inventory</option>
                    <option value="Category" {{ request('entity_type') === 'Category' ? 'selected' : '' }}>Category</option>
                    <option value="Brand" {{ request('entity_type') === 'Brand' ? 'selected' : '' }}>Brand</option>
                    <option value="Coupon" {{ request('entity_type') === 'Coupon' ? 'selected' : '' }}>Coupon</option>
                    <option value="Banner" {{ request('entity_type') === 'Banner' ? 'selected' : '' }}>Banner</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors">
                Filter Audit Trail
            </button>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
        <div>
            <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Security Audit Events ({{ $logs->total() }})</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Immutable records with IP addresses and full delta diffs.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                        <tr>
                            <th class="p-4">Timestamp</th>
                            <th class="p-4">Operator</th>
                            <th class="p-4">Action</th>
                            <th class="p-4">Target Entity</th>
                            <th class="p-4">IP Address</th>
                            <th class="p-4">State Delta / Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 font-mono text-[11px]">
                        @forelse($logs as $log)
                            <tr class="hover:bg-neutral-50/50 transition-colors">
                                <td class="p-4 text-neutral-500">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="p-4 text-neutral-900 font-sans font-semibold">
                                    {{ $log->admin ? $log->admin->name : 'System Worker' }}
                                </td>
                                <td class="p-4">
                                    @php
                                        $actionBadge = [
                                            'created' => 'bg-emerald-50 text-emerald-700',
                                            'updated' => 'bg-blue-50 text-blue-700',
                                            'deleted' => 'bg-rose-50 text-rose-700',
                                            'status_updated' => 'bg-purple-50 text-purple-700',
                                            'stock_adjusted' => 'bg-amber-50 text-amber-700',
                                        ];
                                        $cls = $actionBadge[$log->action] ?? 'bg-neutral-100 text-neutral-700';
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $cls }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="p-4 text-neutral-800">
                                    {{ $log->entity_type }} #{{ $log->entity_id }}
                                </td>
                                <td class="p-4 text-neutral-400">
                                    {{ $log->ip_address ?: '127.0.0.1' }}
                                </td>
                                <td class="p-4 text-neutral-600 max-w-md truncate">
                                    @if($log->new_values)
                                        <span class="text-neutral-500">{{ Str::limit(json_encode($log->new_values), 80) }}</span>
                                    @else
                                        <span class="text-neutral-400 italic">None</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-neutral-400 font-sans text-xs">No audit logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-neutral-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
