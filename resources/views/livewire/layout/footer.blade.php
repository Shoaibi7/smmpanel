<?php
use Livewire\Volt\Component;
new class extends Component {}; 
?>

<footer class="bg-secondary-50 dark:bg-secondary-950 border-t border-secondary-100 dark:border-secondary-900 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="col-span-1 md:col-span-1">
                <a href="/" class="flex items-center space-x-2 mb-6">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center shadow-lg shadow-primary-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold tracking-tight text-secondary-900 dark:text-white">SMM<span class="text-primary-600">PRO</span></span>
                </a>
                <p class="text-secondary-600 dark:text-secondary-400 text-sm leading-relaxed mb-6">
                    Boost your social presence with the world's most reliable SMM panel. Fast delivery, 24/7 support, and unbeatable prices.
                </p>
                <div class="flex space-x-4">
                    <!-- Social icons placeholder -->
                    <a href="#" class="w-8 h-8 rounded-full bg-secondary-200 dark:bg-secondary-800 flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all">
                        <i class="fab fa-facebook-f text-xs"></i>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-secondary-200 dark:bg-secondary-800 flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all">
                        <i class="fab fa-twitter text-xs"></i>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-secondary-200 dark:bg-secondary-800 flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all">
                        <i class="fab fa-instagram text-xs"></i>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="text-secondary-900 dark:text-white font-bold mb-6">Quick Links</h4>
                <ul class="space-y-4 text-sm">
                    <li><a href="/services" class="text-secondary-600 dark:text-secondary-400 hover:text-primary-600 transition-colors">Services</a></li>
                    <li><a href="/api-docs" class="text-secondary-600 dark:text-secondary-400 hover:text-primary-600 transition-colors">API Documentation</a></li>
                    <li><a href="/blog" class="text-secondary-600 dark:text-secondary-400 hover:text-primary-600 transition-colors">Latest News</a></li>
                    <li><a href="/terms" class="text-secondary-600 dark:text-secondary-400 hover:text-primary-600 transition-colors">Terms of Service</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-secondary-900 dark:text-white font-bold mb-6">Support</h4>
                <ul class="space-y-4 text-sm">
                    <li><a href="/faq" class="text-secondary-600 dark:text-secondary-400 hover:text-primary-600 transition-colors">F.A.Q</a></li>
                    <li><a href="/contact" class="text-secondary-600 dark:text-secondary-400 hover:text-primary-600 transition-colors">Contact Us</a></li>
                    <li><a href="/privacy" class="text-secondary-600 dark:text-secondary-400 hover:text-primary-600 transition-colors">Privacy Policy</a></li>
                    <li><a href="/ticket" class="text-secondary-600 dark:text-secondary-400 hover:text-primary-600 transition-colors">Support Ticket</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-secondary-900 dark:text-white font-bold mb-6">Newsletter</h4>
                <p class="text-secondary-600 dark:text-secondary-400 text-sm mb-6">Subscribe to get updates on new services and special offers.</p>
                <form class="space-y-3">
                    <x-text-input type="email" placeholder="Your email address" class="w-full text-sm py-2.5" />
                    <x-button variant="primary" class="w-full py-2.5">Subscribe Now</x-button>
                </form>
            </div>
        </div>

        <div class="pt-8 border-t border-secondary-100 dark:border-secondary-900 flex flex-col md:flex-row justify-between items-center text-xs text-secondary-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <div class="flex space-x-6 mt-4 md:mt-0">
                <span class="flex items-center"><svg class="w-4 h-4 me-1 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg> Safe SSL Encryption</span>
                <span class="flex items-center"><svg class="w-4 h-4 me-1 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg> 24/7 Support</span>
            </div>
        </div>
    </div>
</footer>
