<x-marketing-layout>
    <section class="pt-32 pb-20 bg-secondary-50 dark:bg-secondary-900/50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-4xl font-bold text-secondary-900 dark:text-white mb-4">Frequently Asked Questions</h1>
                <p class="text-secondary-600 dark:text-secondary-400">Everything you need to know about our SMM services.</p>
            </div>

            <div class="space-y-4" x-data="{ active: null }">
                <!-- FAQ 1 -->
                <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 overflow-hidden transition-all shadow-sm">
                    <button @click="active !== 1 ? active = 1 : active = null" class="w-full flex items-center justify-between p-6 text-left">
                        <span class="font-bold text-secondary-900 dark:text-white">What is an SMM Panel?</span>
                        <svg class="w-5 h-5 text-secondary-400 transition-transform" :class="active === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 1" x-collapse>
                        <div class="px-6 pb-6 text-sm text-secondary-600 dark:text-secondary-400 leading-relaxed">
                            An SMM (Social Media Marketing) panel is an online shop that offers various social media services like followers, likes, views, and more at wholesale prices to help individuals and businesses boost their social presence.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 overflow-hidden transition-all shadow-sm">
                    <button @click="active !== 2 ? active = 2 : active = null" class="w-full flex items-center justify-between p-6 text-left">
                        <span class="font-bold text-secondary-900 dark:text-white">Is it safe for my accounts?</span>
                        <svg class="w-5 h-5 text-secondary-400 transition-transform" :class="active === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 2" x-collapse>
                        <div class="px-6 pb-6 text-sm text-secondary-600 dark:text-secondary-400 leading-relaxed">
                            Yes, our services are 100% safe. We use high-quality profiles and drip-feed methods (when applicable) to ensure that your account growth looks natural and complies with social media platform guidelines.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 overflow-hidden transition-all shadow-sm">
                    <button @click="active !== 3 ? active = 3 : active = null" class="w-full flex items-center justify-between p-6 text-left">
                        <span class="font-bold text-secondary-900 dark:text-white">How long does delivery take?</span>
                        <svg class="w-5 h-5 text-secondary-400 transition-transform" :class="active === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 3" x-collapse>
                        <div class="px-6 pb-6 text-sm text-secondary-600 dark:text-secondary-400 leading-relaxed">
                            Most orders start within 0-60 minutes after placement. The completion time depends on the service and quantity ordered, but we pride ourselves on having the fastest delivery speeds in the industry.
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-16 text-center">
                <p class="text-secondary-600 dark:text-secondary-400 mb-6">Still have questions?</p>
                <x-button variant="outline" onclick="window.location.href='/contact'">Contact Support Team</x-button>
            </div>
        </div>
    </section>
</x-marketing-layout>
