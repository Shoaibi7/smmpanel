<x-marketing-layout>
    <section class="pt-32 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-4xl font-bold text-secondary-900 dark:text-white mb-4">Contact Us</h1>
                <p class="text-secondary-600 dark:text-secondary-400">We're here to help you solve any issues or answer your questions.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Info -->
                <div class="space-y-8">
                    <div class="bg-primary-600 rounded-3xl p-10 text-white shadow-2xl shadow-primary-500/30">
                        <h3 class="text-2xl font-bold mb-6">Direct Support</h3>
                        <p class="opacity-80 mb-8 leading-relaxed">Our support team is available 24/7 to assist you. Average response time is under 15 minutes during business hours.</p>
                        
                        <div class="space-y-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold opacity-60 uppercase tracking-widest">Email</p>
                                    <p class="font-bold">support@smmpanel.pro</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold opacity-60 uppercase tracking-widest">Live Chat</p>
                                    <p class="font-bold">Available via Dashboard</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-secondary-50 dark:bg-secondary-900 rounded-3xl p-10 border border-secondary-100 dark:border-secondary-800">
                        <h4 class="text-xl font-bold text-secondary-900 dark:text-white mb-4">Business Inquiries</h4>
                        <p class="text-sm text-secondary-600 dark:text-secondary-400 mb-6">Interested in becoming a reseller or integrating our API? Reach out to our business team.</p>
                        <a href="mailto:business@smmpanel.pro" class="text-primary-600 font-bold hover:underline">business@smmpanel.pro</a>
                    </div>
                </div>

                <!-- Contact Form -->
                <x-card class="p-8 lg:p-12">
                    <form class="space-y-6" onsubmit="event.preventDefault(); alert('Message sent! We will get back to you soon.');">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" value="Full Name" />
                                <x-text-input id="name" type="text" class="mt-1 block w-full" placeholder="John Doe" required />
                            </div>
                            <div>
                                <x-input-label for="email" value="Email Address" />
                                <x-text-input id="email" type="email" class="mt-1 block w-full" placeholder="john@example.com" required />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="subject" value="Subject" />
                            <x-text-input id="subject" type="text" class="mt-1 block w-full" placeholder="How can we help?" required />
                        </div>

                        <div>
                            <x-input-label for="message" value="Message" />
                            <textarea id="message" rows="5" class="mt-1 block w-full border-secondary-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-secondary-300 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-primary-500 dark:focus:ring-primary-400 rounded-md shadow-sm transition-colors duration-200" placeholder="Describe your issue in detail..." required></textarea>
                        </div>

                        <x-button variant="primary" size="lg" class="w-full">Send Message</x-button>
                    </form>
                </x-card>
            </div>
        </div>
    </section>
</x-marketing-layout>
