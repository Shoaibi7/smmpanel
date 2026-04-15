<x-marketing-layout>
    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Accents -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full pointer-events-none">
            <div class="absolute top-20 -left-20 w-72 h-72 bg-primary-500/10 blur-[100px] rounded-full"></div>
            <div class="absolute top-40 -right-20 w-80 h-80 bg-primary-600/5 blur-[100px] rounded-full"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-xs font-bold uppercase tracking-wider mb-8 animate-fade-in">
                    <span class="flex h-2 w-2 rounded-full bg-primary-600 me-2"></span>
                    Number #1 Trusted SMM Panel
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-extrabold text-secondary-900 dark:text-white leading-tight mb-8 tracking-tight">
                    Skyrocket Your Social <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-emerald-400">Presence Instantly</span>
                </h1>
                
                <p class="text-xl text-secondary-600 dark:text-secondary-400 mb-12 max-w-2xl mx-auto leading-relaxed">
                    Boost your followers, likes, and engagement across all major platforms with our lightning-fast, secure, and affordable SMM services.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <x-button variant="primary" size="xl" class="w-full sm:w-auto px-10 shadow-xl shadow-primary-500/20" onclick="window.location.href='{{ route('register') }}'">
                        Start Scaling Now
                    </x-button>
                </div>

                <!-- Trust Badge -->
                <div class="mt-16 flex flex-wrap justify-center items-center gap-8 opacity-60">
                    <div class="flex items-center space-x-2 grayscale hover:grayscale-0 transition-all cursor-default">
                        <span class="text-2xl font-bold">100K+</span>
                        <span class="text-xs uppercase font-semibold">Active Users</span>
                    </div>
                    <div class="flex items-center space-x-2 grayscale hover:grayscale-0 transition-all cursor-default">
                        <span class="text-2xl font-bold">5M+</span>
                        <span class="text-xs uppercase font-semibold">Orders Completed</span>
                    </div>
                    <div class="flex items-center space-x-2 grayscale hover:grayscale-0 transition-all cursor-default">
                        <span class="text-2xl font-bold">4.9/5</span>
                        <span class="text-xs uppercase font-semibold">User Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-secondary-50 dark:bg-secondary-900/50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-3xl lg:text-4xl font-bold text-secondary-900 dark:text-white mb-4">Why Choose SMM PRO?</h2>
                <div class="w-20 h-1.5 bg-primary-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <x-card class="group hover:border-primary-500/50 transition-all duration-300">
                    <div class="w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-secondary-900 dark:text-white">Instant Delivery</h3>
                    <p class="text-secondary-600 dark:text-secondary-400">Our system is fully automated. Your orders start within seconds, not hours.</p>
                </x-card>

                <!-- Feature 2 -->
                <x-card class="group hover:border-primary-500/50 transition-all duration-300">
                    <div class="w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-secondary-900 dark:text-white">Lowest Prices</h3>
                    <p class="text-secondary-600 dark:text-secondary-400">As a direct provider, we offer the most competitive wholesale prices in the market.</p>
                </x-card>

                <!-- Feature 3 -->
                <x-card class="group hover:border-primary-500/50 transition-all duration-300">
                    <div class="w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-secondary-900 dark:text-white">24/7 Support</h3>
                    <p class="text-secondary-600 dark:text-secondary-400">Our dedicated support team is available around the clock to assist you with any questions.</p>
                </x-card>
            </div>
        </div>
    </section>

    <!-- Services Table Section (Preview) -->
    <section class="py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-secondary-900 dark:text-white mb-4 text-left">Top Selling Services</h2>
                    <p class="text-secondary-600 dark:text-secondary-400 max-w-xl">Check out our most popular services across major platforms.</p>
                </div>
            </div>

            <x-card class="p-0 border-none shadow-2xl overflow-hidden">
                <x-table :headers="['Service', 'Rate per 1,000', 'Min/Max', 'Status']">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-secondary-900 dark:text-white">Instagram Real Followers</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600 font-bold">$0.85</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500">100 / 100,000</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Working</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-secondary-900 dark:text-white">TikTok Viral Views</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600 font-bold">$0.001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500">1,000 / 1,000,000</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Instant</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-secondary-900 dark:text-white">Facebook Page Likes (HQ)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-primary-600 font-bold">$1.20</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500">50 / 50,000</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Safe</span>
                        </td>
                    </tr>
                </x-table>
            </x-card>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-primary-600 to-emerald-600 rounded-3xl p-12 lg:p-20 relative overflow-hidden shadow-2xl shadow-primary-500/40">
                <!-- Decorative Circle -->
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 text-center max-w-3xl mx-auto">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-white mb-8 tracking-tight">Ready to Boost Your Influence?</h2>
                    <p class="text-xl text-primary-50 mb-12 opacity-90 leading-relaxed">
                        Join thousands of influencers and businesses who use SMM PRO to grow their community everyday.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <x-button variant="secondary" size="xl" class="w-full sm:w-auto px-12 py-4 bg-white text-primary-700 hover:bg-primary-50">Create Free Account</x-button>
                        <span class="text-white font-medium opacity-80 hidden sm:block">or</span>
                        <a href="/faq" class="text-white font-bold underline underline-offset-8 decoration-white/30 hover:decoration-white transition-all">Check our FAQ</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
