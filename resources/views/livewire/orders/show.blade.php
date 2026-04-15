<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Services\ApiProviderService;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public Order $order;

    public function mount(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $this->order = $order->load('service.category', 'service.apiProvider');
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
            $this->dispatch('toast', message: 'Order status updated.', type: 'success');
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
            $this->dispatch('toast', message: 'Cancel request sent.', type: 'success');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Failed to cancel: ' . $e->getMessage(), type: 'error');
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
        if (str_contains($raw, 'progress') || str_contains($raw, 'processing') || str_contains($raw, 'in progress')) {
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

@php
    $layout = 'app';
@endphp

<div class="space-y-6 py-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-[9px] font-black text-orange-500 uppercase tracking-widest">Order #{{ $order->id }}</div>
            <div class="text-[12px] font-black text-secondary-900 dark:text-white tracking-tight line-clamp-1">{{ $order->service->name }}</div>
        </div>
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-secondary-800 border border-secondary-100 dark:border-secondary-700 text-[9px] font-black uppercase tracking-widest text-secondary-700 dark:text-white hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:border-orange-300 dark:hover:border-orange-700 hover:text-orange-700 dark:hover:text-orange-400 transition-all shadow-sm" wire:navigate>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    <div class="flex items-center justify-between gap-3">
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

        @if($order->api_order_id)
            <div class="flex items-center gap-2">
                <button type="button" wire:click="syncStatus" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-secondary-800 border border-secondary-100 dark:border-secondary-700 text-[9px] font-black uppercase tracking-widest text-secondary-700 dark:text-white hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 hover:text-blue-700 dark:hover:text-blue-400 transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0014-7 9 9 0 00-14-7" /></svg>
                    Sync Status
                </button>
                <button type="button" wire:click="cancelOrder" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-secondary-800 border border-secondary-100 dark:border-secondary-700 text-[9px] font-black uppercase tracking-widest text-secondary-700 dark:text-white hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:border-rose-300 dark:hover:border-rose-700 hover:text-rose-700 dark:hover:text-rose-400 transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    Cancel
                </button>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-secondary-900 rounded-xl border border-secondary-100 dark:border-secondary-800 p-4 shadow-sm">
            <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-2">Service</div>
            <div class="text-[12px] font-black text-secondary-900 dark:text-white">{{ $order->service->name }}</div>
            <div class="text-[9px] font-bold text-secondary-500 uppercase tracking-widest">{{ $order->service->category?->name }}</div>
        </div>
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
        <div class="bg-white dark:bg-secondary-900 rounded-xl border border-secondary-100 dark:border-secondary-800 p-4 shadow-sm">
            <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-2">Target</div>
            <a href="{{ $order->link }}" target="_blank" class="text-[10px] font-bold text-secondary-500 hover:text-orange-600 transition-colors break-all">{{ $order->link }}</a>
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

