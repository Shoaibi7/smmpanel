<?php

use Livewire\Volt\Component;
use App\Models\Category;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Services\ApiProviderService;

new class extends Component {
    public $categories;
    public $services = [];
    
    public $categoryId = '';
    public $serviceId = '';
    public $quantity = 0;
    public $link = '';
    public $charge = 0;
    public $commentsText = '';
    
    public $selectedService = null;

    public function requiresComments(): bool
    {
        if (!$this->selectedService) {
            return false;
        }

        $rawType = strtolower((string) ($this->selectedService->type ?? ''));
        $type = preg_replace('/[^a-z0-9]+/', '_', $rawType);
        $type = trim((string) $type, '_');
        if (in_array($type, ['custom_comments', 'custom_comments_package', 'comment_replies'], true) || str_contains($type, 'comment')) {
            return true;
        }

        $categoryName = strtolower((string) ($this->selectedService->category?->name ?? ''));
        return str_contains($categoryName, 'comment');
    }

    public function mount()
    {
        $this->categories = Category::where('is_active', true)->orderBy('sort_order')->get();
    }

    public function updatedCategoryId($value)
    {
        $this->services = Service::where('category_id', $value)->where('is_active', true)->get();
        $this->serviceId = '';
        $this->selectedService = null;
        $this->commentsText = '';
        $this->calculateCharge();
    }

    public function updatedServiceId($value)
    {
        $this->selectedService = Service::find($value);
        if ($this->selectedService && $this->quantity < $this->selectedService->min_qty) {
            $this->quantity = $this->selectedService->min_qty;
        }
        if (!$this->requiresComments()) {
            $this->commentsText = '';
        }
        $this->calculateCharge();
    }

    public function updatedQuantity()
    {
        $this->calculateCharge();
    }

    public function updatedCommentsText()
    {
        if (!$this->requiresComments()) {
            return;
        }

        $lines = preg_split('/\r\n|\r|\n/', (string) $this->commentsText);
        $count = collect($lines)
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->count();

        if ($count > 0) {
            $this->quantity = $count;
        }

        $this->calculateCharge();
    }

    public function calculateCharge()
    {
        if ($this->selectedService && $this->quantity > 0) {
            $price = (float) $this->selectedService->sale_price;
            if (!($price > 0)) {
                $this->charge = 0;
                return;
            }

            $rawType = strtolower((string) ($this->selectedService->type ?? 'default'));
            $type = preg_replace('/[^a-z0-9]+/', '_', $rawType);
            $type = trim((string) $type, '_');

            // Determine if the price is per 1000 or per unit
            // Standard SMM types that are priced per unit (not per 1000)
            $isPerUnit = in_array($type, ['package', 'custom_comments_package', 'comment_replies'], true) || str_contains($type, 'package');

            if ($isPerUnit) {
                $this->charge = $price * $this->quantity;
            } else {
                $this->charge = ($price / 1000) * $this->quantity;
            }
        } else {
            $this->charge = 0;
        }
    }

    public function placeOrder()
    {
        $rules = [
            'serviceId' => [
                'required',
                \Illuminate\Validation\Rule::exists('services', 'id')->where(function ($q) {
                    $q->where('is_active', true);
                }),
            ],
            'quantity' => [
                'required', 
                'integer', 
                'min:1',
                'max:100000000'
            ],
            'link' => 'required|url',
        ];
        if ($this->requiresComments()) {
            $rules['commentsText'] = ['required', 'string', 'min:1', 'max:20000'];
        }
        $this->validate($rules);

        $service = \App\Models\Service::where('id', $this->serviceId)
            ->where('is_active', true)
            ->select('id', 'sale_price', 'min_qty', 'max_qty', 'type', 'category_id', 'api_provider_id', 'api_service_id')
            ->with(['category:id,name', 'apiProvider:id,api_url,api_key,short_name,status'])
            ->first();
        if (!$service) {
            $this->dispatch('toast', message: 'Service is not available.', type: 'error');
            return;
        }
        $price = (float)($service->sale_price ?? 0);
        if (!($price > 0)) {
            $this->dispatch('toast', message: 'Service price not set. Please select another service.', type: 'error');
            return;
        }

        $comments = null;
        $rawType = strtolower((string) ($service->type ?? ''));
        $type = preg_replace('/[^a-z0-9]+/', '_', $rawType);
        $type = trim((string) $type, '_');
        $categoryName = strtolower((string) ($service->category?->name ?? ''));
        $requiresComments = in_array($type, ['custom_comments', 'custom_comments_package', 'comment_replies'], true) || str_contains($type, 'comment') || str_contains($categoryName, 'comment');
        if ($requiresComments) {
            $lines = preg_split('/\r\n|\r|\n/', (string) $this->commentsText);
            $comments = collect($lines)
                ->map(fn ($v) => trim((string) $v))
                ->filter(fn ($v) => $v !== '')
                ->values()
                ->all();

            if (count($comments) === 0) {
                $this->dispatch('toast', message: 'Please add at least one comment.', type: 'error');
                return;
            }
        }

        $quantityForOrder = $requiresComments ? count($comments ?? []) : (int) $this->quantity;
        if ($quantityForOrder < (int) ($service->min_qty ?? 1) || $quantityForOrder > (int) ($service->max_qty ?? 1000000)) {
            $this->dispatch('toast', message: 'Quantity must be between ' . number_format((int) $service->min_qty) . ' and ' . number_format((int) $service->max_qty) . ' for this service.', type: 'error');
            return;
        }

        // Standard SMM types that are priced per unit (not per 1000)
        $isPerUnit = in_array($type, ['package', 'custom_comments_package', 'comment_replies'], true) || str_contains($type, 'package');

        if ($isPerUnit) {
            $charge = $price * $quantityForOrder;
        } else {
            $charge = ($price / 1000) * $quantityForOrder;
        }

        $apiOrderId = null;
        $shouldSubmitToProvider = $service->api_provider_id && $service->api_service_id && $service->apiProvider && $service->apiProvider->status === 'enabled';
        if ($shouldSubmitToProvider) {
            try {
                $providerService = new ApiProviderService($service->apiProvider);
                $res = $providerService->createOrder(
                    (string) $service->api_service_id,
                    (string) $this->link,
                    $quantityForOrder,
                    $comments ? ['comments' => $comments, 'url' => (string) $this->link] : ['url' => (string) $this->link]
                );
                $apiOrderId = (string) ($res['order_id'] ?? '');
                if ($apiOrderId === '') {
                    throw new \RuntimeException('Provider did not return order id');
                }
            } catch (\Throwable $e) {
                $this->dispatch('toast', message: 'Provider order failed: ' . $e->getMessage(), type: 'error');
                return;
            }
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($charge, $comments, $apiOrderId, $shouldSubmitToProvider, $service, $quantityForOrder) {
                $user = \App\Models\User::whereKey(Auth::id())->lockForUpdate()->first();
                if (!$user || (float)$user->balance < (float)$charge) {
                    throw new \RuntimeException('Insufficient balance');
                }
                Order::create([
                    'user_id' => $user->id,
                    'service_id' => $this->serviceId,
                    'quantity' => $quantityForOrder,
                    'charge' => $charge,
                    'link' => $this->link,
                    'comments' => $comments,
                    'api_order_id' => $apiOrderId ?: null,
                    'status' => $shouldSubmitToProvider ? 'processing' : 'pending',
                ]);
                $user->decrement('balance', (float)$charge);
            });
        } catch (\Throwable $e) {
            if ($shouldSubmitToProvider && $apiOrderId) {
                try {
                    $providerService = new ApiProviderService($service->apiProvider);
                    $providerService->cancelOrder($apiOrderId);
                } catch (\Throwable $ignored) {
                }
            }
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
            return;
        }

        $this->reset(['serviceId', 'quantity', 'link', 'charge', 'commentsText', 'selectedService']);
        
        $this->dispatch('toast', message: 'Order placed successfully!', type: 'success');
    }
}; ?>

@php
    $layout = 'app';
@endphp

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-black text-secondary-900 dark:text-white uppercase tracking-tight">Place New Order</h2>
            <p class="text-[9px] text-secondary-500 font-bold uppercase tracking-widest mt-0.5">Get high quality engagement instantly</p>
        </div>
        <div class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-600/10 rounded-xl border border-emerald-100 dark:border-emerald-600/20 text-emerald-600 font-black text-[9px] uppercase tracking-widest">
            Balance: {{ format_currency(Auth::user()->balance) }}
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Form -->
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 p-6 shadow-sm">
                <form wire:submit.prevent="placeOrder" class="space-y-5">
                    <!-- Category Selection -->
                    <div class="space-y-2">
                        <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest ml-1">Select Platform / Category</label>
                        <select wire:model.live="categoryId" class="w-full px-4 py-3 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[11px] font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none appearance-none">
                            <option value="">Choose a category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Service Selection -->
                    <div class="space-y-3">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest ml-1">Choose Service</label>
                            <select wire:model.live="serviceId" @disabled(empty($services)) class="w-full px-4 py-3 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[11px] font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none appearance-none disabled:opacity-50">
                                <option value="">Select a service...</option>
                                @foreach($services as $svc)
                                    @php
                                        $displayPrice = $svc->sale_price;
                                    @endphp
                                    <option value="{{ $svc->id }}" @disabled(!($displayPrice > 0))>{{ $svc->name }} - {{ format_currency($displayPrice) }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($selectedService)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-secondary-50 dark:bg-black/20 rounded-xl p-4 border border-secondary-100 dark:border-secondary-800">
                                    <h5 class="text-[9px] font-black text-secondary-900 dark:text-white uppercase tracking-widest mb-2">Service</h5>
                                    <div class="space-y-1">
                                        <div class="text-[11px] font-black text-secondary-900 dark:text-white">{{ $selectedService->name }}</div>
                                        <div class="text-[9px] font-bold text-secondary-500 uppercase tracking-widest">{{ $selectedService->category?->name }}</div>
                                    </div>
                                </div>
                                <div class="bg-secondary-50 dark:bg-black/20 rounded-xl p-4 border border-secondary-100 dark:border-secondary-800">
                                    <h5 class="text-[9px] font-black text-secondary-900 dark:text-white uppercase tracking-widest mb-2">Pricing & Limits</h5>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-widest">
                                            <span class="text-secondary-400">Price / 1k</span>
                                            <span class="text-orange-600">{{ format_currency($selectedService->sale_price) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-widest">
                                            <span class="text-secondary-400">Min</span>
                                            <span class="text-secondary-900 dark:text-white">{{ number_format($selectedService->min_qty) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-widest">
                                            <span class="text-secondary-400">Max</span>
                                            <span class="text-secondary-900 dark:text-white">{{ number_format($selectedService->max_qty) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-secondary-50 dark:bg-black/20 rounded-xl p-4 border border-secondary-100 dark:border-secondary-800 md:col-span-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-black text-secondary-400 uppercase tracking-widest">Network</span>
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-500/10 text-emerald-600 px-2.5 py-1 rounded-full border border-emerald-500/20 text-[9px] font-black uppercase">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                {{ $selectedService->is_active ? 'Stable' : 'Offline' }}
                                            </span>
                                        </div>
                                        @if($selectedService->average_time)
                                            <div class="text-[9px] font-black text-secondary-400 uppercase tracking-widest">
                                                <span class="mr-2">Avg Time</span>
                                                <span class="text-secondary-900 dark:text-white bg-secondary-100 dark:bg-secondary-800 px-2 py-0.5 rounded-full border border-secondary-200 dark:border-secondary-700 text-[9px] font-black">
                                                    {{ is_numeric($selectedService->average_time) ? $selectedService->average_time . ' min' : $selectedService->average_time }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Target Link -->
                    <div class="space-y-2">
                        <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest ml-1">Target Link / URL</label>
                        <input wire:model="link" type="text" placeholder="https://instagram.com/p/..." class="w-full px-4 py-3 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[11px] font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none">
                        @error('link') <span class="text-[9px] text-red-500 font-bold uppercase tracking-wider ml-1 mt-1">{{ $message }}</span> @enderror
                    </div>

                    @if($selectedService && $this->requiresComments())
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest ml-1">Comments <span class="text-red-500">*</span></label>
                            <textarea wire:model.live.debounce.250ms="commentsText" rows="5" placeholder="Write one comment per line" class="w-full px-4 py-3 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[11px] font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none"></textarea>
                            <div class="text-[8px] font-black text-secondary-400 uppercase tracking-widest ml-1">One comment per line</div>
                            @error('commentsText') <span class="text-[9px] text-red-500 font-bold uppercase tracking-wider ml-1 mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Quantity Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest ml-1">Quantity</label>
                            <input wire:model.live="quantity" type="number" @readonly($selectedService && $this->requiresComments()) class="w-full px-4 py-3 bg-secondary-50 dark:bg-black/20 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[11px] font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none">
                            <div class="flex justify-between px-1">
                                <span class="text-[8px] font-black text-secondary-400 uppercase">Min: {{ $selectedService ? number_format($selectedService->min_qty) : 0 }}</span>
                                <span class="text-[8px] font-black text-secondary-400 uppercase">Max: {{ $selectedService ? number_format($selectedService->max_qty) : 0 }}</span>
                            </div>
                            @error('quantity') <span class="text-[9px] text-red-500 font-bold uppercase tracking-wider ml-1 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest ml-1">Total Charge</label>
                            <div class="w-full px-4 py-3 bg-orange-600/5 dark:bg-orange-600/10 border border-orange-600/20 rounded-xl text-[12px] font-black text-orange-600 flex items-center">
                                {{ format_currency($charge) }}
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-black text-[11px] uppercase tracking-[0.18em] shadow-xl shadow-orange-600/20 active:scale-[0.98] transition-all border border-orange-400/20 group">
                            Place Order Now
                            <svg class="w-3.5 h-3.5 inline-block ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order Summary & Details Sidebar -->
        <div class="space-y-4">
            @if($selectedService)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-white dark:bg-secondary-900 rounded-xl p-4 border border-secondary-100 dark:border-secondary-800 shadow-sm">
                        <div class="text-[9px] font-black uppercase tracking-widest text-orange-600 mb-2">Order Summary</div>
                        <div class="text-[12px] font-black text-secondary-900 dark:text-white">{{ $selectedService->name }}</div>
                        <div class="text-[9px] font-bold text-secondary-500 uppercase tracking-widest">{{ $selectedService->category?->name }}</div>
                    </div>
                    <div class="bg-white dark:bg-secondary-900 rounded-xl p-4 border border-secondary-100 dark:border-secondary-800 shadow-sm">
                        <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-2">Price / 1k</div>
                        <div class="text-[12px] font-black text-orange-600">{{ format_currency($selectedService->sale_price) }}</div>
                    </div>
                    <div class="bg-white dark:bg-secondary-900 rounded-xl p-4 border border-secondary-100 dark:border-secondary-800 shadow-sm">
                        <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-2">Limits</div>
                        <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                            <span class="text-secondary-400">Min</span>
                            <span class="text-secondary-900 dark:text-white">{{ number_format($selectedService->min_qty) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest mt-1">
                            <span class="text-secondary-400">Max</span>
                            <span class="text-secondary-900 dark:text-white">{{ number_format($selectedService->max_qty) }}</span>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-secondary-900 rounded-xl p-4 border border-secondary-100 dark:border-secondary-800 shadow-sm">
                        <div class="text-[9px] font-black uppercase tracking-widest text-secondary-400 mb-2">Network</div>
                        <span class="inline-flex items-center gap-1.5 bg-emerald-500/10 text-emerald-600 px-2.5 py-1 rounded-full border border-emerald-500/20 text-[9px] font-black uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            {{ $selectedService->is_active ? 'Stable' : 'Offline' }}
                        </span>
                        @if($selectedService->average_time)
                            <div class="mt-2 text-[9px] font-black text-secondary-400 uppercase tracking-widest flex items-center justify-between">
                                <span>Avg Time</span>
                                <span class="text-secondary-900 dark:text-white bg-secondary-100 dark:bg-secondary-800 px-2 py-0.5 rounded-full border border-secondary-200 dark:border-secondary-700 text-[9px] font-black whitespace-nowrap">
                                    @php
                                        // Clean non-numeric characters first to ensure clean parsing
                                        $cleanTime = preg_replace('/[^0-9]/', '', $selectedService->average_time);
                                        $minutes = intval($cleanTime);
                                        
                                        if ($minutes > 0) {
                                            $hours = floor($minutes / 60);
                                            $remainingMinutes = $minutes % 60;
                                            
                                            if ($hours > 0 && $remainingMinutes > 0) {
                                                echo "{$hours} hr {$remainingMinutes} min";
                                            } elseif ($hours > 0) {
                                                echo "{$hours} hr";
                                            } else {
                                                echo "{$minutes} min";
                                            }
                                        } else {
                                            // Fallback if parsing fails or 0, just show original
                                            echo $selectedService->average_time;
                                        }
                                    @endphp
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Selection Placeholder -->
                <div class="bg-white dark:bg-secondary-900 rounded-xl p-6 border border-dashed border-secondary-200 dark:border-secondary-800 flex flex-col items-center justify-center text-center py-16">
                    <div class="w-14 h-14 bg-secondary-50 dark:bg-secondary-800 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-[9px] font-black text-secondary-400 uppercase tracking-[0.2em] max-w-[18ch]">Choose a service to see more details</p>
                </div>
            @endif

            <!-- Mini FAQ / Notice -->
            <div class="bg-orange-600/5 dark:bg-orange-600/10 rounded-xl p-6 border border-orange-600/20">
                <h4 class="text-[9px] font-black text-orange-600 uppercase tracking-widest mb-3">Important Notice</h4>
                <ul class="space-y-2">
                    <li class="flex items-start gap-2 text-[9px] font-bold text-secondary-600 dark:text-secondary-400">
                        <svg class="w-3 h-3 text-orange-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Double check your links before ordering.
                    </li>
                    <li class="flex items-start gap-2 text-[9px] font-bold text-secondary-600 dark:text-secondary-400">
                        <svg class="w-3 h-3 text-orange-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Ensure profiles are set to PUBLIC.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
