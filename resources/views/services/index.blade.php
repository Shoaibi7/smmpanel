<x-marketing-layout>
    <section class="pt-32 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-secondary-900 dark:text-white mb-4">Our Services & Pricing</h1>
                <p class="text-secondary-600 dark:text-secondary-400 max-w-xl mx-auto">Real prices, no hidden fees. Browse all services before signing up.</p>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('services.public') }}" class="flex flex-col sm:flex-row gap-3 mb-8">
                <div class="flex-1 relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search services..."
                        class="w-full pl-9 pr-4 py-2.5 border border-secondary-200 dark:border-secondary-800 rounded-xl bg-white dark:bg-secondary-900 text-sm text-secondary-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <select name="category" onchange="this.form.submit()"
                    class="w-full sm:w-56 px-4 py-2.5 border border-secondary-200 dark:border-secondary-800 rounded-xl bg-white dark:bg-secondary-900 text-sm font-bold text-secondary-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Table -->
            <div class="bg-white dark:bg-secondary-900/40 rounded-2xl border border-secondary-100 dark:border-secondary-800 overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-secondary-50 dark:bg-secondary-900/80 border-b border-secondary-100 dark:border-secondary-800">
                        <tr>
                            <th class="px-5 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">ID</th>
                            <th class="px-5 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">Service Name</th>
                            <th class="px-5 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">Rate / 1,000</th>
                            <th class="px-5 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">Min / Max</th>
                            <th class="px-5 py-3 text-left text-[10px] font-black uppercase tracking-widest text-secondary-400">Features</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-50 dark:divide-secondary-800/50">
                        @forelse($services as $service)
                            <tr class="hover:bg-secondary-50/50 dark:hover:bg-secondary-800/30 transition-colors">
                                <td class="px-5 py-3.5 text-[10px] font-bold text-secondary-400">{{ $service->id }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="text-sm font-semibold text-secondary-900 dark:text-white">{{ $service->name }}</div>
                                    <div class="text-[10px] text-secondary-400 mt-0.5">{{ $service->category?->name }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-sm font-black text-primary-600 dark:text-primary-400">
                                        {{ format_currency($service->sale_price ?? $service->price_per_k) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-secondary-500 font-medium">
                                    {{ number_format($service->min_qty) }} / {{ number_format($service->max_qty) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex gap-1.5 items-center">
                                        @if($service->drip_feed || $service->dripfeed)
                                            <span title="Dripfeed">⚡</span>
                                        @endif
                                        @if($service->refill)
                                            <span title="Refill">♻️</span>
                                        @endif
                                        @if($service->cancel)
                                            <span title="Cancel">✖️</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-sm text-secondary-500">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($services->hasPages())
                    <div class="px-5 py-4 border-t border-secondary-100 dark:border-secondary-800">
                        {{ $services->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

            <!-- CTA -->
            <div class="mt-12 text-center">
                <p class="text-secondary-500 dark:text-secondary-400 text-sm mb-4">Sign up to place orders instantly.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/20">
                    Create Free Account →
                </a>
            </div>

        </div>
    </section>
</x-marketing-layout>
