@extends('layouts.admin')

@section('page_title', 'Customer Directory')
@section('page_subtitle', 'Registered clientele, order history volumes, and contact profiles')

@section('content')
<div class="space-y-6">
    
    <!-- Search Bar -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="relative min-w-[280px] flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customers by name, email, or phone..." class="w-full text-xs bg-neutral-50 border border-neutral-200 rounded-2xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-black">
                <svg class="w-4 h-4 text-neutral-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-neutral-950 text-white font-bold text-xs uppercase tracking-wider hover:bg-neutral-800 transition-colors">
                Search Clients
            </button>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
        <div>
            <div class="p-6 border-b border-neutral-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950">Registered Accounts ({{ $customers->total() }})</h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Overview of user base and transactional engagement.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-neutral-50/80 text-[10px] font-bold uppercase tracking-wider text-neutral-500 border-b border-neutral-100">
                        <tr>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Contact</th>
                            <th class="p-4">Total Orders</th>
                            <th class="p-4">Registered Date</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($customers as $c)
                            <tr class="hover:bg-neutral-50/50 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-neutral-900">{{ $c->name }}</div>
                                    <div class="text-[10px] text-neutral-400 font-mono">ID: #{{ $c->id }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="font-mono text-neutral-700">{{ $c->email }}</div>
                                    <div class="text-[11px] text-neutral-400">{{ $c->phone ?: '--' }}</div>
                                </td>
                                <td class="p-4 font-mono font-bold text-neutral-900">
                                    {{ $c->orders_count }} orders
                                </td>
                                <td class="p-4 font-mono text-[11px] text-neutral-500">
                                    {{ $c->created_at->format('M d, Y') }}
                                </td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('admin.customers.show', $c->id) }}" class="px-3 py-1.5 rounded-xl border border-neutral-300 hover:bg-neutral-900 hover:text-white transition-all text-[11px] font-semibold">
                                        View Profile
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-neutral-400">No customers registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-neutral-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
