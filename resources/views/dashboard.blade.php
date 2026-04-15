<x-app-layout>
    <div class="space-y-6">
        <!-- Hero Welcome Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-secondary-900 via-secondary-800 to-secondary-950 rounded-[2.5rem] p-6 sm:p-8 text-white shadow-2xl border border-secondary-800/50">
            <!-- Background Decorations -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary-600/10 blur-[80px] rounded-full -mr-20 -mt-20 animate-pulse transition-all duration-[3000ms]"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-emerald-500/5 blur-[80px] rounded-full -ml-20 -mb-20"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black font-outfit tracking-tight mb-1.5 leading-none">
                        Welcome back, <span class="text-primary-500">{{ auth()->user()->name }}</span> 
                    </h2>
                    <p class="text-secondary-400 text-[9px] font-bold uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        Status: <span class="text-white">Active Account</span>
                    </p>
                </div>
                
                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="text-right hidden sm:block">
                        <p class="text-[9px] font-black text-secondary-500 uppercase tracking-widest mb-1.5">Current Balance</p>
                        <h3 class="text-2xl font-black font-outfit text-white leading-none">{{ format_currency(auth()->user()->balance) }}</h3>
                    </div>
                    <a href="{{ route('funds.index') }}" wire:navigate class="px-5 py-3 bg-primary-600 hover:bg-primary-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-primary-500/20 active:scale-95 transition-all flex items-center gap-2 border border-white/10 group">
                        <svg class="w-3.5 h-3.5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Funds
                    </a>
                </div>
            </div>
        </div>

        <!-- Compact Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $user = auth()->user();
                $stats = [
                    ['label' => 'Total Orders', 'value' => $user->orders()->count(), 'icon' => 'shopping-cart', 'color' => 'primary'],
                    ['label' => 'Pending', 'value' => $user->orders()->where('status', 'pending')->count(), 'icon' => 'clock', 'color' => 'amber'],
                    ['label' => 'Processing', 'value' => $user->orders()->where('status', 'processing')->count(), 'icon' => 'blue', 'color' => 'blue'],
                    ['label' => 'Completed', 'value' => $user->orders()->where('status', 'completed')->count(), 'icon' => 'check-circle', 'color' => 'emerald'],
                ];
            @endphp

            @foreach($stats as $stat)
                @php $color = $stat['color'] ?? 'blue'; @endphp
                <div class="bg-white dark:bg-secondary-900 p-4 rounded-[2rem] border border-secondary-100 dark:border-secondary-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-12 h-12 bg-{{ $color === 'primary' ? 'primary' : $color }}-500/5 dark:bg-{{ $color === 'primary' ? 'primary' : $color }}-500/10 rounded-full group-hover:scale-[3] transition-transform duration-700"></div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-{{ $color === 'primary' ? 'primary' : $color }}-50 dark:bg-{{ $color === 'primary' ? 'primary' : $color }}-500/20 text-{{ $color === 'primary' ? 'primary' : $color }}-600 dark:text-{{ $color === 'primary' ? 'primary' : $color }}-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($stat['icon'] === 'shopping-cart')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                @elseif($stat['icon'] === 'clock')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @elseif($stat['icon'] === 'check-circle')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                @endif
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[8px] font-black text-secondary-500 dark:text-secondary-400 uppercase tracking-[0.2em] leading-none mb-1">{{ $stat['label'] }}</p>
                            <h4 class="text-base sm:text-lg font-black font-outfit text-secondary-900 dark:text-white truncate tracking-tight">{{ $stat['value'] }}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Quick Actions & Announcement -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Quick Actions Grid -->
                <div>
                    <h3 class="text-[10px] font-black text-secondary-600 dark:text-secondary-400 uppercase tracking-[0.3em] mb-4 flex items-center gap-2">
                        <span class="w-3 h-[2px] rounded-full bg-primary-600"></span>
                        Quick Access
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <a href="{{ route('orders.create') }}" class="flex flex-col items-center justify-center p-5 bg-white dark:bg-secondary-900 rounded-[2.5rem] border border-secondary-100 dark:border-secondary-800 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-950/40 text-primary-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-primary-600 group-hover:text-white group-hover:rotate-12 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <span class="text-[9px] font-black text-secondary-900 dark:text-white uppercase tracking-[0.2em] text-center">New Order</span>
                        </a>
                        
                        <a href="{{ route('orders.index') }}" class="flex flex-col items-center justify-center p-5 bg-white dark:bg-secondary-900 rounded-[2.5rem] border border-secondary-100 dark:border-secondary-800 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-950/40 text-blue-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-blue-600 group-hover:text-white group-hover:rotate-12 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <span class="text-[9px] font-black text-secondary-900 dark:text-white uppercase tracking-[0.2em] text-center">Orders</span>
                        </a>

                        <a href="{{ route('funds.index') }}" class="flex flex-col items-center justify-center p-5 bg-white dark:bg-secondary-900 rounded-[2.5rem] border border-secondary-100 dark:border-secondary-800 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 group">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white group-hover:rotate-12 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <span class="text-[9px] font-black text-secondary-900 dark:text-white uppercase tracking-[0.2em] text-center">Add Funds</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Orders Section -->
                <div class="bg-white dark:bg-secondary-900 rounded-[2.5rem] border border-secondary-100 dark:border-secondary-800 overflow-hidden shadow-sm hover:shadow-md transition-all">
                    <div class="px-8 py-6 border-b border-secondary-50 dark:border-secondary-800 flex justify-between items-center">
                        <div>
                            <h3 class="text-xs font-black text-secondary-900 dark:text-white uppercase tracking-tight font-outfit">Recent Orders</h3>
                            <p class="text-[8px] text-secondary-400 font-bold uppercase tracking-[0.2em] mt-0.5">Real-time status updates</p>
                        </div>
                        <a href="{{ route('orders.index') }}" class="text-[9px] font-black text-primary-600 dark:text-primary-500 uppercase tracking-widest hover:text-primary-700 transition-all inline-flex items-center gap-1 group">
                            Full History <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    
                    <div class="p-8 text-center py-16 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-secondary-50 dark:bg-black/10 transition-opacity opacity-0 group-hover:opacity-100"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 bg-secondary-100 dark:bg-secondary-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:rotate-6 group-hover:scale-110 transition-all">
                                <svg class="w-6 h-6 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <p class="text-[9px] font-black text-secondary-500 uppercase tracking-[0.3em] mb-4">No active orders found</p>
                            <x-button variant="primary" size="sm" class="!px-6 !rounded-xl !text-[9px] !font-black uppercase tracking-widest" onclick="window.location.href='{{ route('orders.create') }}'">Start Ordering</x-button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Account Details & News -->
            <div class="space-y-6">
                <!-- Account Summary -->
                <div class="bg-white dark:bg-secondary-900 rounded-[2.5rem] border border-secondary-100 dark:border-secondary-800 p-6 shadow-sm">
                    <h3 class="text-[10px] font-black text-secondary-900 dark:text-white uppercase tracking-[0.3em] mb-6">Account Summary</h3>
                    
                    <div class="space-y-3">
                        @foreach([
                            ['label' => 'Pricing Status', 'value' => 'Basic Tier', 'color' => 'primary'],
                            ['label' => 'Total Investment', 'value' => format_currency(auth()->user()->orders()->where('status', 'completed')->sum('charge')), 'color' => 'secondary'],
                            ['label' => 'Support Tickets', 'value' => '0 Open', 'color' => 'secondary'],
                        ] as $item)
                        <div class="flex items-center justify-between p-3.5 bg-secondary-50/50 dark:bg-black/20 rounded-2xl border border-transparent hover:border-secondary-100 dark:hover:border-secondary-800 transition-all group">
                            <span class="text-[9px] font-black text-secondary-500 uppercase tracking-widest group-hover:text-secondary-700 dark:group-hover:text-secondary-300 transition-colors">{{ $item['label'] }}</span>
                            <span class="text-[9px] font-black {{ $item['color'] === 'primary' ? 'text-primary-600 dark:text-primary-500' : 'text-secondary-900 dark:text-white' }} tracking-widest">{{ $item['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Trending / Announcements -->
                <div class="relative overflow-hidden bg-gradient-to-br from-primary-600 to-indigo-700 rounded-[2.5rem] p-6 text-white shadow-xl shadow-primary-500/20 border border-white/5 group">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10 text-center sm:text-left">
                        <div class="flex items-center justify-center sm:justify-start gap-2 mb-3">
                            <div class="p-1.5 bg-white/20 rounded-lg">
                                <svg class="w-3.5 h-3.5 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.167a2.406 2.406 0 00-1.21-1.291L1.24 11.013a1.76 1.76 0 01.592-3.417h13.045c.928 0 1.789.364 2.43 1.005l.001.001a1.76 1.76 0 010 2.48l-.001.001z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M18 13l2.857 2.857a.5.5 0 00.857-.353V10.5a.5.5 0 00-.857-.354L18 13z"/></svg>
                            </div>
                            <h3 class="text-[9px] font-black uppercase tracking-[0.3em]">System News</h3>
                        </div>
                        <p class="text-[11px] font-bold leading-relaxed opacity-90 mb-4">
                            We've just added 50+ new High-Quality Instagram services! Check the new rates in our services section.
                        </p>
                        <button class="w-full sm:w-auto text-[9px] font-black uppercase tracking-widest bg-white text-primary-700 hover:bg-primary-50 px-5 py-2.5 rounded-xl transition-all shadow-sm active:scale-95">Explore Services</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
