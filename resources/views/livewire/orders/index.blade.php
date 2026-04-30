<?php

use Livewire\Volt\Component;
use App\Models\Order;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function with()
    {
        return [
            'orders' => Order::where('user_id', Auth::id())
                ->with('service.category', 'service.apiProvider')
                ->when($this->search, function ($q) {
                    $q->where(function ($q) {
                        $q->where('id', 'like', '%' . $this->search . '%')
                          ->orWhere('api_order_id', 'like', '%' . $this->search . '%')
                          ->orWhere('link', 'like', '%' . $this->search . '%')
                          ->orWhereHas('service', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                    });
                })
                ->latest()
                ->paginate(15),
        ];
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function newOrder(): void
    {
        if (Auth::user()->is_blocked) {
            $this->dispatch('toast', message: 'Your account has been blocked. You cannot place new orders. Please contact support.', type: 'error');
            return;
        }

        $this->redirect(route('orders.create'), navigate: true);
    }
}; ?>

@php
    $layout = 'app';
@endphp

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-secondary-900 dark:text-white uppercase tracking-tight">Order History</h2>
            <p class="text-[10px] text-secondary-500 font-bold uppercase tracking-widest mt-1">Manage and track your social activities</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative hidden sm:block">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search orders..." class="pl-10 pr-4 py-2 bg-white dark:bg-secondary-900 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none w-64">
                <svg class="w-4 h-4 text-secondary-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button wire:click="newOrder" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-orange-500/20 active:scale-95 transition-all">
                New Order
            </button>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-secondary-900 rounded-[2rem] border border-secondary-100 dark:border-secondary-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-secondary-100 dark:border-secondary-800">
                        <th class="px-6 py-5 text-[10px] font-black text-secondary-400 uppercase tracking-[0.2em]">Order Detail</th>
                        <th class="px-6 py-5 text-[10px] font-black text-secondary-400 uppercase tracking-[0.2em]">Target Link</th>
                        <th class="px-6 py-5 text-[10px] font-black text-secondary-400 uppercase tracking-[0.2em]">Quantity</th>
                        <th class="px-6 py-5 text-[10px] font-black text-secondary-400 uppercase tracking-[0.2em]">Start Count</th>
                        <th class="px-6 py-5 text-[10px] font-black text-secondary-400 uppercase tracking-[0.2em]">Charge</th>
                        <th class="px-6 py-5 text-[10px] font-black text-secondary-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-secondary-400 uppercase tracking-[0.2em]">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-50 dark:divide-secondary-800/50">
                    @forelse($orders as $order)
                        <tr class="group hover:bg-secondary-50/50 dark:hover:bg-secondary-800/20 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1">
                                    <a href="{{ route('orders.show', $order->id) }}" class="text-[9px] font-black text-orange-500 uppercase tracking-widest hover:text-orange-600 transition-colors" wire:navigate>#{{ $order->id }}</a>
                                    <a href="{{ route('orders.show', $order->id) }}" class="text-xs font-black text-secondary-900 dark:text-white tracking-tight hover:text-orange-600 transition-colors" wire:navigate>{{ $order->service->name }}</a>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <a href="{{ $order->link }}" target="_blank" class="text-[10px] font-bold text-secondary-500 hover:text-orange-500 transition-colors flex items-center gap-1 max-w-[180px]">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span class="truncate">{{ Str::limit($order->link, 30) }}</span>
                                </a>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="text-xs font-black text-secondary-900 dark:text-white">{{ number_format($order->quantity) }}</span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center sm:text-left">
                                <span class="text-xs font-black text-secondary-500 dark:text-secondary-400">{{ number_format($order->start_count ?? 0) }}</span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-orange-600 dark:text-orange-500">{{ format_currency($order->charge) }}</span>
                                    <span class="text-[8px] font-bold text-secondary-400 uppercase tracking-widest mt-0.5">Paid</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'pending' => ['color' => 'amber', 'label' => 'Pending'],
                                        'processing' => ['color' => 'blue', 'label' => 'In Progress'],
                                        'completed' => ['color' => 'emerald', 'label' => 'Completed'],
                                        'partial' => ['color' => 'orange', 'label' => 'Partial'],
                                        'cancelled' => ['color' => 'red', 'label' => 'Cancelled'],
                                        'refunded' => ['color' => 'indigo', 'label' => 'Refunded'],
                                    ][$order->status] ?? ['color' => 'secondary', 'label' => $order->status];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 bg-{{ $statusConfig['color'] }}-500/10 text-{{ $statusConfig['color'] }}-600 px-3 py-1.5 rounded-full border border-{{ $statusConfig['color'] }}-500/20 text-[9px] font-black uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $statusConfig['color'] }}-600 shadow-[0_0_8px_rgba(var(--tw-shadow-color),0.5)]"></span>
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="text-[10px] font-bold text-secondary-400 uppercase tracking-widest">{{ $order->created_at->format('M d, Y') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="w-16 h-16 bg-secondary-50 dark:bg-secondary-800 rounded-2xl flex items-center justify-center mx-auto mb-4 opacity-40">
                                    <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <p class="text-[10px] font-black text-secondary-400 uppercase tracking-[0.2em]">No records found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-8 py-6 bg-secondary-50 dark:bg-secondary-900 border-t border-secondary-100 dark:border-secondary-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
