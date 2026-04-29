<aside    <aside
        id="sidebar"
        class="fixed inset-y-0 left-0 bg-white/80 dark:bg-secondary-900/80 backdrop-blur-xl w-64 border-r border-secondary-200/50 dark:border-secondary-800/50 transition-all duration-300 transform z-30 lg:translate-x-0 lg:static lg:inset-0 shadow-2xl lg:shadow-none flex flex-col overflow-hidden group/sidebar"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        @click.away="sidebarOpen = false"
    >
        <!-- Noise Texture Overlay -->
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-40 brightness-100 contrast-150 mix-blend-overlay pointer-events-none z-0"></div>

        <!-- Brand Info -->
        <div class="h-20 flex items-center px-6 relative z-10 shrink-0">
            <a href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard')) : '/' }}" class="flex items-center gap-3 group w-full" wire:navigate>
                <div class="relative">
                    <div class="absolute inset-0 bg-orange-600 blur opacity-20 group-hover:opacity-40 transition-opacity rounded-xl"></div>
                    <div class="relative w-9 h-9 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-105 transition-transform duration-300 border border-orange-400/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                </div>
                <div class="flex flex-col justify-center">
                    <h1 class="text-lg font-black font-outfit tracking-tighter text-secondary-900 dark:text-white leading-none">SMM<span class="text-orange-500">PRO</span></h1>
                    <span class="text-[9px] font-bold text-secondary-400 uppercase tracking-widest leading-none mt-0.5 opacity-80">Dashboard</span>
                </div>
            </a>
        </div>

        <!-- Navigation Scroll -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-4 space-y-8 scroll-smooth [&::-webkit-scrollbar]:hidden [-ms-overflow-style:'none'] [scrollbar-width:'none'] relative z-10">
            
            <!-- Main Section -->
            <div class="space-y-1">
                <x-sidebar-link :href="auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard')) : '/'" :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')" icon="home" wire:navigate>
                    Dashboard
                </x-sidebar-link>
                @if(auth()->check() && auth()->user()->role !== 'admin')
                <x-sidebar-link :href="route('orders.create')" :active="request()->routeIs('orders.create')" icon="plus-circle" wire:navigate>
                    New Order
                </x-sidebar-link>
                <x-sidebar-link :href="route('orders.index')" :active="request()->routeIs('orders.index')" icon="shopping-bag" wire:navigate>
                    My Orders
                </x-sidebar-link>
                <x-sidebar-link :href="route('funds.index')" :active="request()->routeIs('funds.index')" icon="cash" wire:navigate>
                    Add Funds
                </x-sidebar-link>
                <x-sidebar-link :href="route('api.access')" :active="request()->routeIs('api.access')" icon="plug" wire:navigate>
                    API Access
                </x-sidebar-link>
                @endif
            </div>

            @if(auth()->check() && auth()->user()->role === 'admin')
            <!-- Admin Section -->
            <div>
                <div class="px-3 flex items-center justify-between mb-3">
                    <h3 class="text-[10px] font-black text-secondary-900 dark:text-white uppercase tracking-widest opacity-40">Administration</h3>
                    <div class="h-px flex-1 bg-gradient-to-r from-secondary-200 dark:from-secondary-800 to-transparent ml-3"></div>
                </div>
                <div class="space-y-1">

                    <x-sidebar-link :href="route('admin.orders')" :active="request()->routeIs('admin.orders*')" icon="clipboard-list" wire:navigate>
                        Orders
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.deposits')" :active="request()->routeIs('admin.deposits')" icon="cash" wire:navigate>
                        Deposits
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.api-providers.index')" :active="request()->routeIs('admin.api-providers.*')" icon="plug" wire:navigate>
                        API Provider
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')" icon="archive" wire:navigate>
                        Services
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.categories')" :active="request()->routeIs('admin.categories')" icon="template" wire:navigate>
                        Categories
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" icon="users" wire:navigate>
                        Users
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.blog')" :active="request()->routeIs('admin.blog')" icon="pencil-alt" wire:navigate>
                        Blog
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')" icon="cog" wire:navigate>
                        Settings
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.api')" :active="request()->routeIs('admin.api')" icon="plug" wire:navigate>
                        API
                    </x-sidebar-link>
                </div>
            </div>
            @endif

            <!-- Support Section -->
            <div>
                <div class="px-3 flex items-center justify-between mb-3">
                    <h3 class="text-[10px] font-black text-secondary-900 dark:text-white uppercase tracking-widest opacity-40">Support</h3>
                    <div class="h-px flex-1 bg-gradient-to-r from-secondary-200 dark:from-secondary-800 to-transparent ml-3"></div>
                </div>
                <div class="space-y-1">
                    <x-sidebar-link :href="route('faq')" :active="request()->routeIs('faq')" icon="question-mark-circle" wire:navigate>
                        Help Center
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('contact')" :active="request()->routeIs('contact')" icon="mail" wire:navigate>
                        Contact Support
                    </x-sidebar-link>
                </div>
            </div>
        </div>

        <!-- User Profile Card -->
        <div class="p-4 relative z-10 shrink-0">
            @if(auth()->check() && auth()->user()->role !== 'admin')
            <!-- Balance Card -->
            <div class="mb-2 px-3 py-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-800/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[9px] font-black text-secondary-500 dark:text-secondary-400 uppercase tracking-widest">Balance</span>
                </div>
                <a href="{{ route('funds.index') }}" wire:navigate class="text-[11px] font-black text-emerald-700 dark:text-emerald-400 font-outfit hover:text-emerald-500 transition-colors">
                    {{ format_currency(auth()->user()?->balance ?? 0) }}
                </a>
            </div>
            @endif
            <div class="p-3 bg-secondary-50/50 dark:bg-black/20 backdrop-blur-md rounded-2xl border border-secondary-200/50 dark:border-secondary-700/50 flex items-center gap-3">
                <div class="relative shrink-0">
                    <img class="w-9 h-9 rounded-lg object-cover border border-secondary-200 dark:border-secondary-700" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'User') }}&background=f97316&color=fff" alt="User">
                    <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-orange-500 border-2 border-white dark:border-secondary-900 rounded-full"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-secondary-900 dark:text-white truncate">{{ auth()->user()?->name ?? 'Guest' }}</p>
                    <p class="text-[10px] text-secondary-500 dark:text-secondary-400 truncate">Online</p>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 text-secondary-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Sign Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

</content>
