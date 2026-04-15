<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Models\User;
use App\Models\Service;

new class extends Component {
    public function with()
    {
        return [
            'totalOrders' => Order::count(),
            'totalUsers' => User::count(),
            'totalServices' => Service::count(),
            'recentOrders' => Order::with('user', 'service')->latest()->take(5)->get(),
        ];
    }
}; ?>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black text-secondary-900 dark:text-white mt-4 tracking-tight uppercase">Admin Dashboard</h2>
                <p class="text-[10px] uppercase font-bold text-secondary-400 tracking-wider mt-1">System Overview & Management</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Admin Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Orders -->
                <div class="relative overflow-hidden bg-white dark:bg-secondary-900 p-6 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 group transition-all hover:shadow-md">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-orange-600/5 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="flex items-center gap-4 relative">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 text-orange-600 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-secondary-400 uppercase tracking-widest leading-none mb-1.5">Total Orders</p>
                            <h4 class="text-2xl font-black text-secondary-900 dark:text-white leading-none">{{ $totalOrders }}</h4>
                        </div>
                    </div>
                </div>
                
                <!-- Registered Users -->
                <div class="relative overflow-hidden bg-white dark:bg-secondary-900 p-6 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 group transition-all hover:shadow-md">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-600/5 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="flex items-center gap-4 relative">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-secondary-400 uppercase tracking-widest leading-none mb-1.5">Total Users</p>
                            <h4 class="text-2xl font-black text-secondary-900 dark:text-white leading-none">{{ $totalUsers }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Active Services -->
                <div class="relative overflow-hidden bg-white dark:bg-secondary-900 p-6 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 group transition-all hover:shadow-md">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-emerald-600/5 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="flex items-center gap-4 relative">
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-secondary-400 uppercase tracking-widest leading-none mb-1.5">Active Services</p>
                            <h4 class="text-2xl font-black text-secondary-900 dark:text-white leading-none">{{ $totalServices }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 my-8">
                <!-- Recent Orders -->
                <div class="bg-white dark:bg-secondary-900 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 flex flex-col overflow-hidden">
                    <div class="px-6 py-5 border-b border-secondary-100 dark:border-secondary-800 flex items-center justify-between">
                        <h3 class="text-sm font-black text-secondary-900 dark:text-white uppercase tracking-tight">Recent Orders</h3>
                        <a href="{{ route('admin.orders') }}" class="text-[10px] font-black text-orange-600 uppercase tracking-widest hover:text-orange-700 transition-colors">View All &rarr;</a>
                    </div>

                    <div class="p-4 space-y-2">
                        @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between p-3 bg-secondary-50 dark:bg-secondary-800/50 rounded-xl border border-secondary-100/50 dark:border-secondary-700/50 transition-all hover:border-orange-200 dark:hover:border-orange-900/30 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-secondary-800 flex items-center justify-center shadow-sm border border-secondary-100 dark:border-secondary-700 group-hover:bg-orange-600 group-hover:text-white transition-all text-secondary-400">
                                        <span class="text-[10px] font-black">#{{ substr($order->id, -3) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-xs font-black text-secondary-900 dark:text-white leading-none mb-1 uppercase tracking-tight">{{ $order->user->name ?? 'Guest' }}</p>
                                        <p class="text-[9px] text-secondary-500 font-bold truncate max-w-[180px] leading-none">{{ $order->service?->name ?? 'Service deleted' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-black text-orange-600 dark:text-orange-400 leading-none mb-1">{{ format_currency($order->charge) }}</p>
                                    <span class="text-[8px] uppercase font-black px-2 py-0.5 rounded-full {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20' : 'bg-orange-50 text-orange-600 dark:bg-orange-900/20' }}">{{ $order->status }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Management -->
                <div class="bg-white dark:bg-secondary-900 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 flex flex-col overflow-hidden">
                    <div class="px-6 py-5 border-b border-secondary-100 dark:border-secondary-800">
                        <h3 class="text-sm font-black text-secondary-900 dark:text-white uppercase tracking-tight">Quick Management</h3>
                    </div>

                    <div class="p-6 grid grid-cols-2 gap-4">
                        <a href="{{ route('admin.services.index') }}" class="h-28 flex flex-col items-center justify-center space-y-3 bg-orange-50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-900/30 rounded-2xl text-orange-600 hover:bg-orange-100 dark:hover:bg-orange-900/20 transition-all group">
                             <div class="p-3 bg-white dark:bg-secondary-800 rounded-xl shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                             </div>
                             <span class="text-[10px] font-black uppercase tracking-widest">Services</span>
                        </a>
                        <a href="{{ route('admin.categories') }}" class="h-28 flex flex-col items-center justify-center space-y-3 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-2xl text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/20 transition-all group">
                             <div class="p-3 bg-white dark:bg-secondary-800 rounded-xl shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                             </div>
                             <span class="text-[10px] font-black uppercase tracking-widest">Categories</span>
                        </a>
                        <a href="{{ route('admin.users') }}" class="h-28 flex flex-col items-center justify-center space-y-3 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/30 rounded-2xl text-emerald-600 hover:bg-emerald-100 dark:hover:bg-emerald-900/20 transition-all group">
                             <div class="p-3 bg-white dark:bg-secondary-800 rounded-xl shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                             </div>
                             <span class="text-[10px] font-black uppercase tracking-widest">Users</span>
                        </a>
                        <a href="{{ route('admin.settings') }}" class="h-28 flex flex-col items-center justify-center space-y-3 bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30 rounded-2xl text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-900/20 transition-all group">
                             <div class="p-3 bg-white dark:bg-secondary-800 rounded-xl shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                             </div>
                             <span class="text-[10px] font-black uppercase tracking-widest">Settings</span>
                        </a>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
