<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SMM Panel') }} - Modern SMM Services</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script>
            if (localStorage.getItem('dark-mode') === 'true' || (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-white dark:bg-secondary-950 text-secondary-900 dark:text-secondary-100 transition-colors duration-200">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav x-data="{ open: false, scrolled: false }" 
                 @scroll.window="scrolled = window.pageYOffset > 20"
                 :class="{ 'bg-white/80 dark:bg-secondary-950/80 backdrop-blur-md border-b border-secondary-100 dark:border-secondary-800': scrolled, 'bg-transparent': !scrolled }"
                 class="fixed w-full z-50 transition-all duration-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-20">
                        <div class="flex items-center">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="/" class="flex items-center space-x-2">
                                    <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <span class="text-xl font-bold tracking-tight text-secondary-900 dark:text-white">SMM<span class="text-primary-600">PRO</span></span>
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:ms-10 sm:flex mt-1">
                                <a href="/blog" class="text-sm font-medium hover:text-primary-600 transition-colors">Blog</a>
                                <a href="/faq" class="text-sm font-medium hover:text-primary-600 transition-colors">FAQ</a>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                            <x-theme-toggle />
                            
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-sm font-medium hover:text-primary-600 transition-colors">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium hover:text-primary-600 transition-colors">Login</a>
                                <x-button variant="primary" size="sm" onclick="window.location.href='{{ route('register') }}'">Get Started</x-button>
                            @endauth
                        </div>

                        <!-- Mobile menu button -->
                        <div class="-me-2 flex items-center sm:hidden space-x-2">
                            <x-theme-toggle />
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-secondary-400 hover:text-secondary-500 hover:bg-secondary-100 dark:hover:bg-secondary-900 focus:outline-none transition ease-in-out duration-150">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white dark:bg-secondary-950 border-b border-secondary-100 dark:border-secondary-800">
                    <div class="pt-2 pb-3 space-y-1 px-4">
                        <a href="/blog" class="block py-2 text-base font-medium">Blog</a>
                        <a href="/faq" class="block py-2 text-base font-medium">FAQ</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="block py-2 text-base font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block py-2 text-base font-medium">Login</a>
                            <a href="{{ route('register') }}" class="block py-2 text-base font-medium text-primary-600">Get Started</a>
                        @endauth
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-grow">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <livewire:layout.footer />
        </div>
        
        <x-toast />
        @livewireScripts
        @stack('scripts')
    </body>
</html>
