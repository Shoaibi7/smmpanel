<?php

use Livewire\Volt\Component;
use App\Models\Service;
use App\Models\Category;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $category = '';

    public function with()
    {
        return [
            'services' => Service::query()
                ->with('category')
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->when($this->category, fn($q) => $q->where('category_id', $this->category))
                ->where('is_active', true)
                ->paginate(20),
            'categories' => Category::where('is_active', true)->get(),
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }
}; ?>

<div>
    <x-marketing-layout>
        <section class="pt-24 pb-16 bg-white dark:bg-secondary-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-secondary-900 dark:text-white mb-2 tracking-tight">Services List</h1>
                    <p class="text-sm text-secondary-500 dark:text-secondary-400">Premium social media marketing services at wholesale prices.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 mb-6">
                    <div class="flex-1 relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" wire:model.live="search" placeholder="Search services..." class="w-full pl-9 pr-4 py-2 text-sm border-secondary-200 dark:border-secondary-800 rounded-xl bg-secondary-50 dark:bg-secondary-900/50 focus:ring-2 focus:ring-primary-500 transition-all">
                    </div>
                    <div class="w-full sm:w-64">
                        <select wire:model.live="category" class="w-full border-secondary-200 dark:border-secondary-800 rounded-xl bg-secondary-50 dark:bg-secondary-900/50 py-2 px-4 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-white dark:bg-secondary-900/30 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800/50 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-secondary-100 dark:divide-secondary-800">
                            <thead class="bg-secondary-50/50 dark:bg-secondary-900/80">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase text-secondary-400 tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase text-secondary-400 tracking-wider">Service</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase text-secondary-400 tracking-wider">Rate/1k</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase text-secondary-400 tracking-wider">Min/Max</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase text-secondary-400 tracking-wider">Features</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-50 dark:divide-secondary-800/50">
                                @forelse($services as $service)
                                    <tr class="hover:bg-secondary-50/50 dark:hover:bg-secondary-800/30 transition-all duration-200">
                                        <td class="px-4 py-3 whitespace-nowrap text-[10px] font-bold text-secondary-400 tracking-tighter">{{ $service->id }}</td>
                                        <td class="px-4 py-3">
                                            <div class="text-xs font-bold text-secondary-900 dark:text-white">{{ $service->name }}</div>
                                            <div class="text-[9px] text-secondary-500 mt-0.5">{{ $service->category->name }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="text-xs font-black text-primary-600 dark:text-primary-400">{{ format_currency($service->sale_price ?? $service->price_per_k) }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-[10px] font-medium text-secondary-500">
                                            {{ number_format($service->min_qty) }} - {{ number_format($service->max_qty) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex gap-1.5">
                                                @if($service->dripfeed || $service->drip_feed)
                                                    <span class="text-[9px]" title="Dripfeed Available">⚡</span>
                                                @endif
                                                @if($service->refill)
                                                    <span class="text-[9px]" title="Refill Guarantee">♻️</span>
                                                @endif
                                                @if($service->cancel)
                                                    <span class="text-[9px]" title="Cancellation Supported">✖️</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-sm text-secondary-500">
                                            No services found matching your criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($services->hasPages())
                        <div class="px-4 py-3 bg-secondary-50/50 dark:bg-secondary-900/50 border-t border-secondary-100 dark:border-secondary-800">
                            {{ $services->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </x-marketing-layout>
</div>
