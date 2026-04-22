<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Services\ApiProviderService;

new class extends Component {
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load('user', 'service.category', 'service.apiProvider');
    }

    public function syncStatus(): void
    {
        $this->order->refresh();
        $this->order->load('service.apiProvider');

        if (!$this->order->api_order_id) {
            $this->dispatch('toast', message: 'No API order id found for this order.', type: 'error');
            return;
        }

        $provider = $this->order->service?->apiProvider;
        if (!$provider || $provider->status !== 'enabled') {
            $this->dispatch('toast', message: 'API provider is not configured/enabled for this service.', type: 'error');
            return;
        }

        try {
            $svc = new ApiProviderService($provider);
            $status = $svc->checkOrderStatus((string) $this->order->api_order_id);

            $raw = strtolower((string) ($status['status'] ?? ''));
            $normalized = $this->normalizeOrderStatus($raw);

            $this->order->update([
                'start_count' => $status['start_count'] ?? $this->order->start_count,
                'remains' => $status['remains'] ?? $this->order->remains,
                'status' => $normalized,
            ]);

            $this->order->refresh();
            $this->dispatch('toast', message: 'Order status synced successfully.', type: 'success');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Failed to sync status: ' . $e->getMessage(), type: 'error');
        }
    }

    public function cancelOrder(): void
    {
        $this->order->refresh();
        $this->order->load('service.apiProvider');

        if (!$this->order->api_order_id) {
            $this->dispatch('toast', message: 'No API order id found for this order.', type: 'error');
            return;
        }

        $provider = $this->order->service?->apiProvider;
        if (!$provider || $provider->status !== 'enabled') {
            $this->dispatch('toast', message: 'API provider is not configured/enabled for this service.', type: 'error');
            return;
        }

        try {
            $svc = new ApiProviderService($provider);
            $svc->cancelOrder((string) $this->order->api_order_id);

            $this->order->update(['status' => 'cancelled']);
            $this->order->refresh();
            $this->dispatch('toast', message: 'Cancel request sent to provider.', type: 'success');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Failed to cancel order: ' . $e->getMessage(), type: 'error');
        }
    }

    private function normalizeOrderStatus(string $raw): string
    {
        $raw = trim($raw);

        if ($raw === '') {
            return $this->order->status;
        }

        if (str_contains($raw, 'complete')) {
            return 'completed';
        }
        if (str_contains($raw, 'partial')) {
            return 'partial';
        }
        if (str_contains($raw, 'cancel')) {
            return 'cancelled';
        }
        if (str_contains($raw, 'refund')) {
            return 'refunded';
        }
        if (str_contains($raw, 'progress') || str_contains($raw, 'processing') || str_contains($raw, 'inprogress') || str_contains($raw, 'in progress')) {
            return 'processing';
        }
        if (str_contains($raw, 'pending')) {
            return 'pending';
        }

        return $this->order->status;
    }

    public function with()
    {
        return [
            'order' => $this->order,
        ];
    }
}; ?>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-black text-secondary-900 dark:text-white tracking-tight">Order #{{ $order->id }}</h2>
            <p class="text-[9px] uppercase font-bold text-secondary-400 tracking-wider mt-0.5">Full details & status</p>
        </div>
    </div>
</x-slot>

<div class="space-y-6 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="px-3 py-1.5 bg-secondary-100 dark:bg-secondary-800 rounded-xl">
                <span class="text-[9px] font-black uppercase tracking-widest text-secondary-500 dark:text-secondary-400">Order</span>
                <span class="text-[13px] font-black text-secondary-900 dark:text-white ml-1">#{{ $order->id }}</span>
            </div>
            <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full
                {{ [
                    'pending' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400',
                    'processing' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400',
                    'completed' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400',
                    'partial' => 'bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400',
                    'cancelled' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400',
                    'refunded' => 'bg-slate-50 text-slate-600 dark:bg-slate-900/20 dark:text-slate-400',
                ][$order->status] ?? 'bg-secondary-50 text-secondary-600' }}">
                {{ strtoupper($order->status) }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            @if($order->api_order_id)
                <button type="button" wire:click="syncStatus" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-secondary-800 border border-secondary-100 dark:border-secondary-700 text-[9px] font-black uppercase tracking-widest text-secondary-700 dark:text-white hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 hover:text-blue-700 dark:hover:text-blue-400 transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0014-7 9 9 0 00-14-7" /></svg>
                    Sync Status
                </button>
                <button type="button" wire:click="cancelOrder" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-secondary-800 border border-secondary-100 dark:border-secondary-700 text-[9px] font-black uppercase tracking-widest text-secondary-700 dark:text-white hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-300 dark:hover:border-rose-700 hover:text-rose-700 dark:hover:text-rose-400 transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    Cancel
                </button>
            @endif
            <a href="{{ route('admin.orders') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-secondary-800 border border-secondary-100 dark:border-secondary-700 text-[9px] font-black uppercase tracking-widest text-secondary-700 dark:text-white hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:border-orange-300 dark:hover:border-orange-700 hover:text-orange-700 dark:hover:text-orange-400 transition-all shadow-sm" wire:navigate>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                Back to Orders
            </a>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- User Card -->
        <div class="bg-white dark:bg-secondary-900 rounded-xl border border-secondary-100 dark:border-secondary-800 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-secondary-50 dark:bg-secondary-800 flex items-center justify-center">
                    <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A6 6 0 1118.88 6.196 9 9 0 105.12 17.804z" /></svg>
                </div>
                <div>
                    <div class="text-[11px] font-black text-secondary-900 dark:text-white uppercase tracking-tight">{{ $order->user->name ?? 'Guest' }}</div>
                    <div class="text-[9px] font-bold text-secondary-500 uppercase tracking-widest">{{ $order->user->email ?? 'No Email' }}</div>
                </div>
            </div>
        </div>

        <!-- Service Card -->
        <div class="bg-white dark:bg-secondary-900 rounded-xl border border-secondary-100 dark:border-secondary-800 p-4 shadow-sm">
            <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-2">Service</div>
            <div class="text-[12px] font-black text-orange-600">{{ $order->service->name }}</div>
            <div class="text-[9px] font-bold text-secondary-500 uppercase tracking-widest mt-0.5">{{ $order->service->category->name }}</div>
            @if($order->service->apiProvider)
                <div class="mt-2 pt-2 border-t border-secondary-100 dark:border-secondary-800 flex items-center gap-1.5">
                    <span class="text-[8px] font-black uppercase tracking-widest text-secondary-400">Provider:</span>
                    <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[8px] font-black uppercase tracking-widest rounded-md">
                        ⚡ {{ $order->service->apiProvider->short_name }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Charge & Quantity Card -->
        <div class="bg-white dark:bg-secondary-900 rounded-xl border border-secondary-100 dark:border-secondary-800 p-4 shadow-sm">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-1">Quantity</div>
                    <div class="text-[12px] font-black text-secondary-900 dark:text-white">{{ number_format($order->quantity) }}</div>
                </div>
                <div>
                    <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-1">Charge</div>
                    <div class="text-[12px] font-black text-orange-600">{{ format_currency($order->charge) }}</div>
                </div>
                <div>
                    <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-1">Created</div>
                    <div class="text-[11px] font-bold text-secondary-600">{{ $order->created_at->format('M d, Y H:i') }}</div>
                </div>
                <div>
                    <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-1">API Order ID</div>
                    <div class="text-[11px] font-bold text-secondary-600">{{ $order->api_order_id ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Target Link -->
    <div class="bg-white dark:bg-secondary-900 rounded-xl border border-secondary-100 dark:border-secondary-800 p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-lg">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.172 13.828a4 4 0 005.656 0l4-4a4 4 0 10-5.656-5.656l-1.102 1.101" /></svg>
                </div>
                <a href="{{ $order->link }}" target="_blank" class="text-[9px] font-bold text-secondary-500 hover:text-orange-600 transition-colors truncate max-w-[420px]">{{ $order->link }}</a>
            </div>
            <a href="{{ $order->link }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-orange-600/10 text-orange-600 text-[9px] font-black uppercase tracking-widest border border-orange-600/20 hover:bg-orange-600/20 transition-colors">
                Open Link
            </a>
        </div>
    </div>

    @if(!empty($order->comments))
        <div class="bg-white dark:bg-secondary-900 rounded-xl border border-secondary-100 dark:border-secondary-800 p-4 shadow-sm">
            <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-2">Comments</div>
            <div class="space-y-1">
                @foreach($order->comments as $c)
                    <div class="text-[11px] font-bold text-secondary-700 dark:text-secondary-300">{{ $c }}</div>
                @endforeach
            </div>
        </div>
    @endif
</div>
