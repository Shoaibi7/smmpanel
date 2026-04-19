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
    <section class="py-24 relative">
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

    <!-- Platform Services Section -->
    @php
        $platforms = [
            ['key' => 'facebook',  'label' => 'Facebook',   'emoji' => '📘'],
            ['key' => 'instagram', 'label' => 'Instagram',  'emoji' => '📸'],
            ['key' => 'tiktok',    'label' => 'TikTok',     'emoji' => '🎵'],
            ['key' => 'youtube',   'label' => 'YouTube',    'emoji' => '▶️'],
            ['key' => 'twitter',   'label' => 'Twitter/X',  'emoji' => '🐦'],
            ['key' => 'pinterest', 'label' => 'Pinterest',  'emoji' => '📌'],
            ['key' => 'snapchat',  'label' => 'Snapchat',   'emoji' => '👻'],
            ['key' => 'spotify',   'label' => 'Spotify',    'emoji' => '🎧'],
        ];
        $platformContent = [
            'facebook'  => ['title' => 'Facebook SMM Services',   'description' => 'Facebook Pages are the gateway for businesses to market to billions of users. A Facebook Page is a public presence similar to a personal profile. Get More Likes, Followers, Post Likes, Video Views, and more — all delivered fast and safely.', 'features' => ['Page Likes','Post Likes','Followers','Video Views','Comments','Shares'], 'image' => 'https://cdn-icons-png.flaticon.com/512/5968/5968764.png'],
            'instagram' => ['title' => 'Instagram SMM Services',  'description' => 'Grow your Instagram presence with real followers, likes, views, and story interactions. Perfect for influencers, brands, and businesses looking to dominate the Instagram algorithm.',                                                                    'features' => ['Followers','Likes','Views','Story Views','Comments','Saves'],           'image' => 'https://cdn-icons-png.flaticon.com/512/2111/2111463.png'],
            'tiktok'    => ['title' => 'TikTok SMM Services',     'description' => 'Boost your TikTok content with viral views, followers, and likes. Our TikTok services help you reach the For You Page faster and grow your audience organically.',                                                                                              'features' => ['Followers','Likes','Views','Shares','Comments','Live Views'],           'image' => 'https://cdn-icons-png.flaticon.com/512/3046/3046121.png'],
            'youtube'   => ['title' => 'YouTube SMM Services',    'description' => 'Increase your YouTube channel authority with real subscribers, views, likes, and watch hours. Accelerate your monetization journey with our safe and effective YouTube services.',                                                                              'features' => ['Subscribers','Views','Likes','Watch Hours','Comments','Shares'],        'image' => 'https://cdn-icons-png.flaticon.com/512/1384/1384060.png'],
            'twitter'   => ['title' => 'Twitter / X SMM Services','description' => 'Amplify your Twitter/X presence with followers, retweets, likes, and impressions. Build credibility and reach a wider audience with our high-quality Twitter services.',                                                                                       'features' => ['Followers','Likes','Retweets','Impressions','Replies','Bookmarks'],     'image' => 'https://cdn-icons-png.flaticon.com/512/5969/5969020.png'],
            'pinterest' => ['title' => 'Pinterest SMM Services',  'description' => 'Drive traffic and grow your Pinterest account with followers, repins, and board followers. Perfect for e-commerce brands and content creators looking to expand their reach.',                                                                                   'features' => ['Followers','Repins','Likes','Board Followers','Views','Comments'],      'image' => 'https://cdn-icons-png.flaticon.com/512/145/145808.png'],
            'snapchat'  => ['title' => 'Snapchat SMM Services',   'description' => 'Grow your Snapchat audience with followers and story views. Reach a younger demographic and increase your brand visibility on one of the most engaging social platforms.',                                                                                      'features' => ['Followers','Story Views','Subscribers','Views','Shares','Saves'],       'image' => 'https://cdn-icons-png.flaticon.com/512/2111/2111703.png'],
            'spotify'   => ['title' => 'Spotify SMM Services',    'description' => 'Boost your music career with Spotify plays, followers, and monthly listeners. Get your tracks noticed by the algorithm and grow your fanbase with our Spotify promotion services.',                                                                             'features' => ['Plays','Followers','Monthly Listeners','Saves','Playlist Adds','Podcast Plays'], 'image' => 'https://cdn-icons-png.flaticon.com/512/174/174872.png'],
        ];
    @endphp

    <section class="py-24 relative overflow-hidden" x-data="{ activeTab: 'facebook' }">
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-primary-600/10 blur-[140px] rounded-full"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-4xl lg:text-5xl font-extrabold text-secondary-900 dark:text-white mb-4">SMM Panel Services</h2>
                <p class="text-secondary-500 dark:text-secondary-400 max-w-2xl mx-auto text-base leading-relaxed">
                    Promote yourself or your company. If you're looking for a way to increase your online presence, you can use our panel at the best and cheapest price.
                </p>
            </div>

            <!-- Platform Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-10 justify-start lg:justify-center" style="scrollbar-width:none;">
                @foreach($platforms as $p)
                    <button
                        @click="activeTab = '{{ $p['key'] }}'"
                        :class="activeTab === '{{ $p['key'] }}'
                            ? 'bg-primary-600 text-white border-primary-500 shadow-lg shadow-primary-500/30 scale-105'
                            : 'bg-secondary-100 dark:bg-secondary-800/80 text-secondary-600 dark:text-secondary-300 border-secondary-200 dark:border-secondary-700 hover:border-primary-500/60 hover:text-primary-600 dark:hover:text-white'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border text-sm font-semibold whitespace-nowrap transition-all duration-200 flex-shrink-0 cursor-pointer"
                    >
                        <span class="text-base leading-none">{{ $p['emoji'] }}</span>
                        {{ $p['label'] }}
                    </button>
                @endforeach
            </div>

            <!-- Tab Panels -->
            @foreach($platforms as $p)
                @php $c = $platformContent[$p['key']]; @endphp
                <div
                    x-show="activeTab === '{{ $p['key'] }}'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-[0.98]"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="rounded-2xl border border-secondary-200 dark:border-secondary-700/60 bg-secondary-50 dark:bg-secondary-800/50 overflow-hidden"
                    style="display: none;"
                >
                    <!-- Inner grid: always 2 cols on md+, stack on mobile -->
                    <div style="display:grid; grid-template-columns: 1fr auto; align-items:center; gap:0; min-height:340px;">

                        <!-- Left: Text content -->
                        <div class="p-8 lg:p-14">
                            <h3 class="text-3xl lg:text-4xl font-extrabold text-secondary-900 dark:text-white mb-5 leading-tight">
                                {{ $c['title'] }}
                            </h3>
                            <p class="text-secondary-600 dark:text-secondary-300 text-base lg:text-lg leading-relaxed mb-7 max-w-xl">
                                {{ $c['description'] }}
                            </p>
                            <div class="flex flex-wrap gap-2 mb-8">
                                @foreach($c['features'] as $feature)
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-secondary-100 dark:bg-secondary-700/60 border border-secondary-200 dark:border-secondary-600/50 text-secondary-700 dark:text-secondary-200 text-sm rounded-full font-medium">
                                        <svg class="w-3.5 h-3.5 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                            <x-button variant="primary" size="lg" onclick="window.location.href='{{ route('register') }}'">
                                Get Started — It's Free
                            </x-button>
                        </div>

                        <!-- Right: Platform logo -->
                        <div class="flex items-center justify-center p-8 lg:p-14" style="width:280px;">
                            <div class="relative">
                                <div class="absolute inset-0 bg-primary-500/20 blur-3xl rounded-full scale-150 pointer-events-none"></div>
                                <img
                                    src="{{ $c['image'] }}"
                                    alt="{{ $p['label'] }}"
                                    style="width:200px; height:200px; object-fit:contain; position:relative; filter:drop-shadow(0 20px 40px rgba(0,0,0,0.5));"
                                    loading="lazy"
                                >
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach

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
