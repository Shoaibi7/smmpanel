<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script>
            window.applyTheme = function() {
                if (localStorage.getItem('dark-mode') === 'true' || (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
            
            window.applyTheme();
            document.addEventListener('livewire:navigated', window.applyTheme);
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-secondary-50 dark:bg-secondary-950 text-secondary-900 dark:text-secondary-100 transition-colors duration-200">
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
            
            <!-- Sidebar Navigation -->
            <x-sidebar />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Navbar -->
                <header class="h-14 flex items-center justify-between px-4 sm:px-8 bg-white/80 dark:bg-secondary-900/80 backdrop-blur-md border-b border-secondary-100 dark:border-secondary-800 transition-colors duration-200 z-20">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = true" class="p-2 -ml-2 rounded-lg text-secondary-500 hover:bg-secondary-100 dark:hover:bg-secondary-800 lg:hidden">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        
                        @if (isset($header))
                            <div class="text-sm font-black text-secondary-900 dark:text-white uppercase tracking-tight">
                                {{ $header }}
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 sm:gap-4">
                        <x-theme-toggle />
                        
                        <!-- Notifications -->
                        <button class="p-2 rounded-xl text-secondary-500 hover:bg-secondary-100 dark:hover:bg-secondary-800 relative group transition-all active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-orange-500 rounded-full border-2 border-white dark:border-secondary-900"></span>
                        </button>

                        <div class="h-6 w-px bg-secondary-100 dark:bg-secondary-800 mx-1 hidden sm:block"></div>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-all group active:scale-95">
                                    <div class="w-7 h-7 rounded-lg bg-orange-600 flex items-center justify-center text-white font-black text-[10px] shadow-lg shadow-orange-500/20">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <div class="hidden sm:block text-left">
                                        <p class="text-[10px] font-black text-secondary-900 dark:text-white leading-none uppercase tracking-widest">{{ auth()->user()->name }}</p>
                                    </div>
                                    <svg class="w-3.5 h-3.5 text-secondary-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-3 border-b border-secondary-100 dark:border-secondary-800">
                                    <p class="text-[9px] text-secondary-500 font-bold uppercase tracking-widest leading-none mb-1.5">Connected Account</p>
                                    <p class="text-xs font-black text-secondary-900 dark:text-white truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <x-dropdown-link :href="route('profile')" wire:navigate class="!text-[10px] font-black uppercase tracking-widest py-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0ZM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                        {{ __('My Profile') }}
                                    </div>
                                </x-dropdown-link>

                                <livewire:layout.navigation_logout />
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-8 space-y-8 scrollbar-thin scrollbar-thumb-orange-600/20 hover:scrollbar-thumb-orange-600/40 dark:scrollbar-thumb-secondary-800 dark:hover:scrollbar-thumb-secondary-700">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <x-toast />
        <!-- jQuery (required for some admin scripts) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- DataTables JS (for admin tables) -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

        <script>
            (() => {
                const isJqueryReady = () => typeof window.$ === 'function' && typeof window.jQuery === 'function';
                const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                const setupAjax = () => {
                    if (!isJqueryReady()) return;
                    window.$.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': window.$('meta[name="csrf-token"]').attr('content')
                        }
                    });
                };

                const waitForJquery = (cb) => {
                    if (isJqueryReady()) return cb();
                    let tries = 0;
                    const interval = setInterval(() => {
                        if (isJqueryReady()) {
                            clearInterval(interval);
                            cb();
                            return;
                        }
                        tries += 1;
                        if (tries > 100) clearInterval(interval);
                    }, 50);
                };

                const runOnceReady = (cb) => {
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', cb, { once: true });
                    } else {
                        cb();
                    }
                };

                runOnceReady(() => waitForJquery(setupAjax));
                document.addEventListener('livewire:navigated', () => waitForJquery(setupAjax));

                window.showLoading = function () {
                let spinner = document.getElementById('global-loading-spinner');
                if (!spinner) {
                    spinner = document.createElement('div');
                    spinner.id = 'global-loading-spinner';
                    spinner.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden';
                    spinner.innerHTML = '<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>';
                    document.body.appendChild(spinner);
                }
                spinner.classList.remove('hidden');
                };

                window.hideLoading = function () {
                const spinner = document.getElementById('global-loading-spinner');
                if (spinner) spinner.classList.add('hidden');
                };

                window.makeRequest = function (url, method = 'POST', data = null) {
                    if (isJqueryReady()) {
                        return new Promise((resolve, reject) => {
                            window.$.ajax({
                                url: url,
                                type: method,
                                data: data,
                                success: function (response) {
                                    resolve(response);
                                },
                                error: function (xhr) {
                                    reject(xhr.responseJSON || { message: xhr.statusText || 'An error occurred' });
                                }
                            });
                        });
                    }

                    const headers = {
                        'X-CSRF-TOKEN': getCsrfToken()
                    };
                    const options = { method, headers };

                    if (data instanceof FormData) {
                        options.body = data;
                    } else if (data !== null && data !== undefined) {
                        headers['Content-Type'] = 'application/json';
                        options.body = typeof data === 'string' ? data : JSON.stringify(data);
                    }

                    return fetch(url, options).then(async (res) => {
                        const contentType = res.headers.get('content-type') || '';
                        const payload = contentType.includes('application/json') ? await res.json() : await res.text();
                        if (!res.ok) {
                            throw (typeof payload === 'object' ? payload : { message: payload });
                        }
                        return payload;
                    });
                };

                window.showToast = function (message, type = 'success') {
                    const bgColor = type === 'success' ? 'bg-emerald-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 p-4 rounded-xl text-white ${bgColor} z-[9999] shadow-2xl animate-in fade-in slide-in-from-right-4 duration-300 font-bold text-xs uppercase tracking-widest border border-white/20 backdrop-blur-md`;
                    toast.textContent = message;
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.style.transition = 'opacity 300ms ease';
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 320);
                    }, 3000);
                };
            })();
        </script>

        @yield('scripts')
    </body>
</html>
