<?php

use Livewire\Volt\Component;
use App\Models\Order;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $statusFilter = '';
    public $search = '';

    public function with()
    {
        return [
            'orders' => Order::query()
                ->with('user', 'service.category', 'service.apiProvider')
                ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
                ->when($this->search, function($q) {
                    $q->where('id', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', fn($qu) => $qu->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhere('link', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(20),
        ];
    }

    public function updateStatus($id, $status)
    {
        Order::find($id)->update(['status' => $status]);
        $this->dispatch('toast', message: 'Order status updated!', type: 'success');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
}; ?>

<div class="space-y-6 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-secondary-200 dark:border-secondary-800 pb-3">
        <div>
            <h2 class="text-lg font-black text-secondary-900 dark:text-white tracking-tight">Order Management</h2>
            <p class="text-[9px] uppercase font-bold text-secondary-400 tracking-wider mt-0.5">Manage all system orders and delivery statuses</p>
        </div>
        
        <div class="flex items-center gap-1.5 bg-secondary-100 dark:bg-secondary-800 p-1 rounded-xl">
            @foreach(['' => 'All', 'pending' => 'Pending', 'processing' => 'Processing', 'completed' => 'Completed'] as $val => $label)
                <button wire:click="$set('statusFilter', '{{ $val }}')" 
                    class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all {{ $statusFilter === $val ? 'shadow-sm bg-white dark:bg-secondary-700 text-orange-600 dark:text-orange-500' : 'text-secondary-500 hover:text-secondary-700 dark:hover:text-secondary-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Premium Search Bar -->
    <div class="bg-white dark:bg-secondary-900 rounded-xl shadow-sm border border-secondary-100 dark:border-secondary-800 p-2">
        <div class="relative flex flex-col md:flex-row gap-2">
            <div class="relative flex-1">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-secondary-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="Search by ID, user, or link..."
                    class="w-full pl-11 pr-4 py-2.5 text-[11px] border-none rounded-lg bg-transparent text-secondary-900 dark:text-white font-medium focus:ring-0 placeholder-secondary-400 uppercase tracking-tight">
                <div class="absolute right-3 top-1/2 -translate-y-1/2 hidden md:block">
                    <div class="px-2 py-1 bg-secondary-100 dark:bg-secondary-800 rounded text-[8px] font-black text-secondary-400 uppercase tracking-wider">
                        SHIFT + S
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List (Card Style) -->
    <div class="space-y-4">
        @if($orders->count() > 0)
            <!-- Table Header -->
            <div class="hidden lg:grid lg:grid-cols-12 gap-3 px-3 py-2.5 bg-secondary-50/50 dark:bg-secondary-900/30 rounded-lg border border-secondary-100 dark:border-secondary-800">
                <div class="col-span-4 text-[9px] uppercase font-black text-secondary-400 tracking-widest flex items-center">User & Service</div>
                <div class="col-span-1 text-[9px] uppercase font-black text-secondary-400 tracking-widest flex items-center justify-center">Start Count</div>
                <div class="col-span-2 text-[9px] uppercase font-black text-secondary-400 tracking-widest flex items-center">Order Target (Link)</div>
                <div class="col-span-1 text-[9px] uppercase font-black text-secondary-400 tracking-widest flex items-center justify-center">Quantity</div>
                <div class="col-span-1 text-[9px] uppercase font-black text-secondary-400 tracking-widest flex items-center justify-end">Charge</div>
                <div class="col-span-2 text-[9px] uppercase font-black text-secondary-400 tracking-widest flex items-center justify-center">Status</div>
                <div class="col-span-1 text-[9px] uppercase font-black text-secondary-400 tracking-widest flex items-center justify-end">Date</div>
            </div>

            <!-- Table Rows -->
            <div class="space-y-2">
                @foreach($orders as $order)
                    <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm border border-secondary-100 dark:border-secondary-700 hover:shadow-md transition-all duration-200 group">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 p-3 items-center">
                            <!-- User & Service (with order ID) -->
                            <div class="col-span-4">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-[9px] font-black text-orange-500 uppercase tracking-widest">#{{ $order->id }}</span>
                                        @if($order->api_order_id)
                                            <span class="text-[9px] font-mono font-bold text-blue-500 dark:text-blue-400">API #{{ $order->api_order_id }}</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-black text-secondary-900 dark:text-white leading-tight uppercase">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:text-orange-600 transition-colors">{{ $order->user->name ?? 'Guest' }}</a>
                                    </div>
                                    <div class="text-[9px] text-orange-600 dark:text-orange-400 font-bold mt-0.5 line-clamp-1 truncate">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:text-orange-700 transition-colors">{{ $order->service?->name ?? 'Service deleted' }}</a>
                                    </div>
                                    <div class="text-[8px] text-secondary-400 uppercase tracking-widest mt-0.5">{{ $order->service?->category?->name ?? 'Uncategorized' }}</div>
                                    @if($order->service?->apiProvider)
                                        <div class="text-[8px] text-blue-500 dark:text-blue-400 font-bold uppercase tracking-widest mt-0.5">
                                            ⚡ {{ $order->service->apiProvider->short_name }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Start Count -->
                            <div class="col-span-1 text-center">
                                <div class="text-[11px] font-black text-secondary-900 dark:text-white">{{ number_format($order->start_count ?? 0) }}</div>
                            </div>

                            <!-- Link -->
                            <div class="col-span-2">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-lg">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.172 13.828a4 4 0 005.656 0l4-4a4 4 0 10-5.656-5.656l-1.102 1.101" /></svg>
                                    </div>
                                    <a href="{{ $order->link }}" target="_blank" class="text-[9px] font-bold text-secondary-500 hover:text-orange-600 transition-colors truncate max-w-[180px]">{{ $order->link }}</a>
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="col-span-1 text-center">
                                <div class="text-[11px] font-black text-secondary-900 dark:text-white">{{ number_format($order->quantity) }}</div>
                                <div class="text-[8px] text-secondary-400 uppercase font-black">UNITS</div>
                            </div>

                            <!-- Charge -->
                            <div class="col-span-1 text-right">
                                <div class="text-[11px] font-black text-orange-600 dark:text-orange-400">{{ format_currency($order->charge) }}</div>
                            </div>

                            <!-- Status -->
                            <div class="col-span-2 flex justify-center">
                                <select 
                                    wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                    class="text-[8.5px] font-black uppercase tracking-widest py-1 px-2.5 rounded-full border-none focus:ring-2 focus:ring-orange-500/20 shadow-sm transition-all cursor-pointer appearance-none text-center
                                    {{ [
                                        'pending' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400',
                                        'processing' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400',
                                        'completed' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400',
                                        'partial' => 'bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400',
                                        'cancelled' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400',
                                        'refunded' => 'bg-slate-50 text-slate-600 dark:bg-slate-900/20 dark:text-slate-400',
                                    ][$order->status] ?? 'bg-secondary-50 text-secondary-600' }}"
                                >
                                    @foreach(['pending', 'processing', 'completed', 'partial', 'cancelled', 'refunded'] as $st)
                                        <option value="{{ $st }}" @selected($order->status == $st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date -->
                            <div class="col-span-1 text-right">
                                <div class="text-[8.5px] font-bold text-secondary-500">{{ $order->created_at->format('M d') }}</div>
                                <div class="text-[8px] text-secondary-400 font-medium">{{ $order->created_at->format('H:i') }}</div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-secondary-900 rounded-2xl border border-dashed border-secondary-200 dark:border-secondary-700">
                <div class="w-16 h-16 rounded-2xl bg-secondary-50 dark:bg-secondary-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
                <h3 class="text-sm font-black text-secondary-900 dark:text-white uppercase tracking-tight">No Orders Found</h3>
                <p class="text-xs text-secondary-500 mt-2">Adjust your filters or try a different search term.</p>
            </div>
        @endif
    </div>
</div>
