@extends('layouts.admin')

@section('title', 'Service Details')

@section('content')
<div class="space-y-4">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.services.index') }}" class="w-8 h-8 rounded-lg bg-white dark:bg-secondary-900 border border-secondary-200 dark:border-secondary-800 flex items-center justify-center text-secondary-500 hover:bg-secondary-50 dark:hover:bg-secondary-800 transition-all shadow-sm group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h3 class="text-sm font-black text-secondary-900 dark:text-white uppercase tracking-tight">Service Details</h3>
                <p class="text-[9px] font-bold text-secondary-400 uppercase tracking-widest mt-0.5">Managing: <span class="text-orange-500 font-black">{{ $service->name }}</span></p>
            </div>
        </div>
        <div class="flex gap-2">
            @php
                $isActive = ($service->status ?? ($service->is_active ? 'active' : 'inactive')) === 'active';
            @endphp
            <button onclick="openEditModalFromShow(this)" 
                data-id="{{ $service->id }}"
                data-name="{{ $service->name }}"
                data-api_provider_id="{{ $service->api_provider_id }}"
                data-api_service_id="{{ $service->api_service_id }}"
                data-category_id="{{ $service->category_id }}"
                data-type="{{ $service->type }}"
                data-rate="{{ $service->rate ?? $service->price_per_k }}"
                data-provider_rate="{{ $service->provider_rate ?? $service->rate ?? $service->price_per_k }}"
                data-sale_price="{{ $service->sale_price ?? $service->price_per_k }}"
                data-price_locked="{{ $service->price_locked ? '1' : '0' }}"
                data-min_order="{{ $service->min_order ?? $service->min_qty }}"
                data-max_order="{{ $service->max_order ?? $service->max_qty }}"
                data-dripfeed="{{ $service->dripfeed || $service->drip_feed ? '1' : '0' }}"
                data-refill="{{ $service->refill ? '1' : '0' }}"
                data-cancel="{{ $service->cancel ? '1' : '0' }}"
                data-description="{{ $service->description }}"
                data-status="{{ $isActive ? 'active' : 'inactive' }}"
                class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-xl text-[9px] font-black shadow-lg shadow-orange-500/10 transition-all active:scale-95 flex items-center uppercase tracking-widest">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Service
            </button>
        </div>
    </div>

    @php
        function formatAverageTime($minutes) {
            if (!$minutes || !is_numeric($minutes) || $minutes <= 0) return 'N/A';
            
            $h = floor($minutes / 60);
            $m = $minutes % 60;
            
            if ($h > 0 && $m > 0) return $h . 'h ' . $m . 'm';
            if ($h > 0) return $h . 'h';
            return $minutes . 'm';
        }
    @endphp

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Provider Cost -->
        <div class="bg-white dark:bg-secondary-900 rounded-2xl p-4 border border-secondary-100 dark:border-secondary-800 shadow-sm transition-all hover:shadow-md group relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-11 h-11 rounded-2xl bg-secondary-50 dark:bg-secondary-800 flex items-center justify-center text-secondary-500 dark:text-secondary-400 border border-secondary-100 dark:border-secondary-700 shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest leading-none mb-1.5">Provider Cost</p>
                    <h4 class="text-lg font-black text-secondary-900 dark:text-white tracking-tighter">${{ number_format($service->provider_rate ?? $service->rate ?? 0, 4) }}</h4>
                </div>
            </div>
        </div>

        <!-- Sale Price - Primary -->
        <div class="bg-white dark:bg-secondary-900 rounded-2xl p-4 border border-secondary-100 dark:border-secondary-800 shadow-sm transition-all hover:shadow-md group relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-11 h-11 rounded-2xl bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-800/50 shadow-inner group-hover:bg-orange-100 dark:group-hover:bg-orange-900/40 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest leading-none mb-1.5">Sale Price</p>
                    <h4 class="text-lg font-black text-orange-500 dark:text-orange-400 tracking-tighter">${{ number_format($service->sale_price ?? $service->price_per_k, 4) }}</h4>
                </div>
            </div>
        </div>

        <!-- Order Limits -->
        <div class="bg-white dark:bg-secondary-900 rounded-2xl p-4 border border-secondary-100 dark:border-secondary-800 shadow-sm transition-all hover:shadow-md group relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800/50 shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest leading-none mb-1.5">Min / Max Orders</p>
                    <h4 class="text-[11px] font-black text-secondary-900 dark:text-white tracking-tight">
                        {{ number_format($service->min_order ?? $service->min_qty) }} <span class="text-secondary-300 font-medium">to</span> {{ number_format($service->max_order ?? $service->max_qty) }}
                    </h4>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white dark:bg-secondary-900 rounded-2xl p-4 border border-secondary-100 dark:border-secondary-800 shadow-sm transition-all hover:shadow-md group relative overflow-hidden">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-11 h-11 rounded-2xl {{ $isActive ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20' }} flex items-center justify-center shadow-inner border border-transparent {{ $isActive ? 'border-emerald-100' : 'border-red-100' }}">
                    <div class="relative">
                        <div class="w-2.5 h-2.5 rounded-full {{ $isActive ? 'bg-emerald-600' : 'bg-red-600' }} {{ $isActive ? 'animate-ping opacity-75' : '' }}"></div>
                        <div class="w-2.5 h-2.5 rounded-full {{ $isActive ? 'bg-emerald-600' : 'bg-red-600' }} absolute inset-0"></div>
                    </div>
                </div>
                <div>
                    <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest leading-none mb-1.5">Current Status</p>
                    <h4 class="text-[10px] font-black {{ $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} tracking-widest uppercase">{{ $isActive ? 'Active' : 'Inactive' }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-secondary-100 dark:border-secondary-800 bg-secondary-50/50 dark:bg-secondary-900/50 flex items-center justify-between">
                    <h4 class="text-[9px] font-black text-secondary-400 uppercase tracking-widest">Service Overview</h4>
                    <span class="text-[9px] font-black px-2 py-0.5 bg-white dark:bg-secondary-800 rounded-lg border border-secondary-100 dark:border-secondary-700 text-secondary-500 uppercase tracking-tighter">ID: #{{ $service->id }}</span>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-0.5">Category Name</p>
                            <p class="text-[11px] font-black text-secondary-900 dark:text-white uppercase tracking-tight">{{ $service->category?->name ?? 'NO CATEGORY' }}</p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-0.5">Service Type</p>
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $service->type }}</span>
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-2.5 ml-0.5">Enabled Features</p>
                            <div class="flex flex-wrap gap-2.5">
                                <div class="flex items-center gap-2 group px-3 py-1.5 bg-secondary-50 dark:bg-secondary-800/50 rounded-xl border border-secondary-100 dark:border-secondary-700/50">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $service->dripfeed || $service->drip_feed ? 'bg-emerald-500' : 'bg-secondary-300' }}"></div>
                                    <span class="text-[9px] font-black uppercase text-secondary-600 dark:text-secondary-400 tracking-widest">Dripfeed</span>
                                </div>
                                <div class="flex items-center gap-2 group px-3 py-1.5 bg-secondary-50 dark:bg-secondary-800/50 rounded-xl border border-secondary-100 dark:border-secondary-700/50">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $service->refill ? 'bg-emerald-500' : 'bg-secondary-300' }}"></div>
                                    <span class="text-[9px] font-black uppercase text-secondary-600 dark:text-secondary-400 tracking-widest">Refill</span>
                                </div>
                                <div class="flex items-center gap-2 group px-3 py-1.5 bg-secondary-50 dark:bg-secondary-800/50 rounded-xl border border-secondary-100 dark:border-secondary-700/50">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $service->cancel ? 'bg-emerald-500' : 'bg-secondary-300' }}"></div>
                                    <span class="text-[9px] font-black uppercase text-secondary-600 dark:text-secondary-400 tracking-widest">Cancel</span>
                                </div>
                                @if($service->average_time)
                                <div class="flex items-center gap-2 group px-3 py-1.5 bg-secondary-50 dark:bg-secondary-800/50 rounded-xl border border-secondary-100 dark:border-secondary-700/50">
                                    <svg class="w-2.5 h-2.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-[9px] font-black uppercase text-secondary-600 dark:text-secondary-400 tracking-widest">Avg Time: {{ formatAverageTime($service->average_time) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-secondary-100 dark:border-secondary-800">
                        <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-2.5 ml-0.5">Service Description</p>
                        <div class="text-[11px] text-secondary-600 dark:text-secondary-400 bg-secondary-50/50 dark:bg-secondary-900/50 p-4 rounded-xl border border-secondary-100 dark:border-secondary-800 leading-relaxed font-medium">
                            {!! !empty($service->description) ? nl2br(e($service->description)) : '<span class="italic opacity-50">No description available for this service.</span>' !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50/50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/20 rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-[11px] font-black text-red-600 dark:text-red-400 uppercase tracking-tight">Danger Zone</h4>
                        <p class="text-[9px] text-red-400 dark:text-red-900/40 font-bold mt-0.5">Permanently remove this service from your database.</p>
                    </div>
                    <button onclick="deleteService({{ $service->id }}, '{{ $service->name }}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-[9px] font-black shadow-lg shadow-red-500/10 transition-all active:scale-95 uppercase tracking-widest">
                        Delete Service
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-4">
            @if($service->api_provider_id)
                <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 overflow-hidden shadow-sm">
                    <div class="p-5 border-b border-secondary-100 dark:border-secondary-800 bg-secondary-50/50 dark:bg-secondary-900/50">
                        <h4 class="text-[9px] font-black text-secondary-400 uppercase tracking-widest">Provider Connection</h4>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex flex-col gap-1">
                            <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest leading-none">Provider Name</p>
                            <a href="{{ route('admin.api-providers.show', $service->apiProvider) }}" class="text-[11px] font-black text-orange-600 hover:underline hover:text-orange-700 uppercase tracking-tight">
                                {{ $service->apiProvider->api_name }}
                            </a>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest leading-none">API Service ID</p>
                            <p class="text-[11px] font-black text-secondary-800 dark:text-white font-mono tracking-tighter">{{ $service->api_service_id }}</p>
                        </div>
                        <div class="pt-4 mt-1 border-t border-secondary-100 dark:border-secondary-800 flex flex-col gap-1">
                            <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest leading-none">Last Synced</p>
                            <p class="text-[9px] font-black text-secondary-500 dark:text-secondary-400 uppercase">{{ $service->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-secondary-900 rounded-2xl border border-secondary-100 dark:border-secondary-800 overflow-hidden shadow-sm">
                <div class="p-5 border-b border-secondary-100 dark:border-secondary-800 bg-secondary-50/50 dark:bg-secondary-900/50">
                    <h4 class="text-[9px] font-black text-secondary-400 uppercase tracking-widest">Audit Info</h4>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex flex-col gap-1">
                        <p class="text-[8px] font-black text-secondary-400 uppercase tracking-widest leading-none">Date Created</p>
                        <p class="text-[10px] font-black text-secondary-700 dark:text-secondary-300 uppercase leading-none">{{ $service->created_at->format('M j, Y - H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div id="editServiceModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-secondary-900/60 backdrop-blur-sm hidden animate-in fade-in duration-300">
    <div class="bg-white dark:bg-secondary-900 rounded-2xl shadow-2xl w-full max-w-lg p-0 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="editModalContainer">
        <!-- Modal Header -->
        <div class="bg-secondary-50 dark:bg-secondary-950/50 px-6 py-4 border-b border-secondary-100 dark:border-secondary-800 flex justify-between items-center">
            <h3 class="text-xs font-black text-secondary-800 dark:text-white flex items-center uppercase tracking-tight">
                <span class="w-7 h-7 rounded-lg bg-orange-600 text-white flex items-center justify-center mr-3 shadow-lg shadow-orange-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </span>
                Edit Service
            </h3>
            <button onclick="closeEditModal()" class="w-7 h-7 rounded-lg flex items-center justify-center text-secondary-400 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
            <form id="editServiceForm" class="space-y-4">
                <input type="hidden" id="edit_service_id">
                <input type="hidden" id="edit_api_service_id_hidden">
                
                <div class="grid grid-cols-2 gap-4">
                    <!-- Provider & Category -->
                    <div>
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">API Provider</label>
                        <select id="edit_api_provider_id" class="w-full px-3 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            <option value="">Internal</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->api_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">Category</label>
                        <select id="edit_category_id" class="w-full px-3 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Name -->
                    <div class="col-span-2">
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">Service Name</label>
                        <input type="text" id="edit_name" class="w-full px-4 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none" placeholder="Enter service name">
                    </div>

                    <!-- Type & Rate -->
                    <div>
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">Service Type</label>
                        <select id="edit_type" class="w-full px-3 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            <option value="default">Default</option>
                            <option value="subscriptions">Subscriptions</option>
                            <option value="custom_comments">Custom Comments</option>
                            <option value="mentions">Mentions</option>
                            <option value="package">Package</option>
                            <option value="comment_likes">Comment Likes</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">Rate (per 1K)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-400 font-bold text-[10px]">$</span>
                            <input type="number" id="edit_rate" step="0.0001" class="w-full pl-7 pr-3 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-black focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">Sale Price (per 1K)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-400 font-bold text-[10px]">$</span>
                            <input type="number" id="edit_sale_price" step="0.0001" class="w-full pl-7 pr-3 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-black focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                        </div>
                    </div>

                    <div class="col-span-2 bg-secondary-50/50 dark:bg-secondary-950/30 rounded-xl p-3 border border-secondary-100 dark:border-secondary-800 flex items-center justify-between gap-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="edit_price_locked" class="w-3 h-3 rounded border-secondary-300 text-orange-600 focus:ring-orange-500 bg-white">
                            <span class="ml-2 text-[9px] font-black text-secondary-500 dark:text-secondary-400 uppercase tracking-widest">Lock Sale Price</span>
                        </label>
                        <div class="text-[9px] font-black uppercase tracking-widest text-secondary-500 dark:text-secondary-400">
                            Profit / 1K: <span id="edit_profit_per_k" class="text-secondary-900 dark:text-white">$0.0000</span>
                        </div>
                    </div>

                    <!-- Min & Max -->
                    <div>
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">Min Order</label>
                        <input type="number" id="edit_min_order" class="w-full px-4 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">Max Order</label>
                        <input type="number" id="edit_max_order" class="w-full px-4 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                    </div>

                    <!-- Features -->
                    <div class="col-span-2 bg-secondary-50/50 dark:bg-secondary-950/30 rounded-xl p-3 border border-secondary-100 dark:border-secondary-800">
                        <div class="grid grid-cols-3 gap-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="edit_dripfeed" class="w-3 h-3 rounded border-secondary-300 text-orange-600 focus:ring-orange-500 bg-white">
                                <span class="ml-2 text-[9px] font-black text-secondary-500 dark:text-secondary-400 uppercase tracking-widest">Dripfeed</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="edit_refill" class="w-3 h-3 rounded border-secondary-300 text-orange-600 focus:ring-orange-500 bg-white">
                                <span class="ml-2 text-[9px] font-black text-secondary-500 dark:text-secondary-400 uppercase tracking-widest">Refill</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="edit_cancel" class="w-3 h-3 rounded border-secondary-300 text-orange-600 focus:ring-orange-500 bg-white">
                                <span class="ml-2 text-[9px] font-black text-secondary-500 dark:text-secondary-400 uppercase tracking-widest">Cancel</span>
                            </label>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-span-2">
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">Service Status</label>
                        <select id="edit_status" class="w-full px-3 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-black focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label class="block text-[8px] font-black text-secondary-400 uppercase tracking-widest mb-1.5 ml-1">Description</label>
                        <textarea id="edit_description" rows="2" class="w-full px-4 py-2 bg-secondary-50 dark:bg-secondary-950/50 border border-secondary-100 dark:border-secondary-800 rounded-xl text-[10px] font-medium focus:ring-2 focus:ring-orange-500 transition-all outline-none resize-none" placeholder="Service details..."></textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-secondary-50 dark:bg-secondary-950/50 border-t border-secondary-100 dark:border-secondary-800 flex justify-end gap-2">
            <button onclick="closeEditModal()" class="px-4 py-2 rounded-xl border border-secondary-200 dark:border-secondary-700 text-secondary-500 font-black text-[9px] uppercase tracking-widest hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-all">
                Cancel
            </button>
            <button id="updateServiceBtn" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-black text-[9px] uppercase tracking-widest shadow-lg shadow-orange-500/20 active:scale-95 transition-all">
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteServiceModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-secondary-900/60 backdrop-blur-sm hidden animate-in fade-in duration-300">
    <div class="bg-white dark:bg-secondary-900 rounded-2xl shadow-2xl w-full max-w-sm p-0 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="deleteModalContainer">
        <div class="p-8 text-center">
            <div class="w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-red-500/10">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-sm font-black text-secondary-800 dark:text-white uppercase tracking-tight mb-2">Are you sure?</h3>
            <p class="text-[10px] text-secondary-500 dark:text-secondary-400 font-bold leading-relaxed mb-6">
                You are about to delete <span id="delete_service_name" class="text-red-600 font-black"></span>. This action cannot be undone.
            </p>
            <div class="flex gap-2">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 rounded-xl border border-secondary-100 dark:border-secondary-800 text-secondary-500 font-black text-[9px] uppercase tracking-widest hover:bg-secondary-50 dark:hover:bg-secondary-800 transition-all">
                    No, Cancel
                </button>
                <button id="confirmDeleteBtn" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-black text-[9px] uppercase tracking-widest shadow-lg shadow-red-500/20 active:scale-95 transition-all">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // --- CSRF Setup for jQuery ---
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function syncService(serviceId) {
        showLoading();
        $.post(`/admin/services/${serviceId}/sync`).done(res => {
            hideLoading();
            showToast(res.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }).fail(xhr => {
            hideLoading();
            showToast(xhr.responseJSON?.message || 'Error syncing service', 'error');
        });
    }

    let serviceIdToDelete = null;

    window.deleteService = function(serviceId, serviceName) {
        console.log('Preparing to delete service:', serviceId, serviceName);
        serviceIdToDelete = serviceId;
        $('#delete_service_name').text(serviceName);
        $('#deleteServiceModal').removeClass('hidden');
        setTimeout(() => $('#deleteModalContainer').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    }

    window.closeDeleteModal = function() {
        $('#deleteModalContainer').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#deleteServiceModal').addClass('hidden'), 300);
    }

    $('#confirmDeleteBtn').on('click', function() {
        if (!serviceIdToDelete) {
            console.error('No service ID to delete');
            return;
        }

        console.log('Sending delete request for ID:', serviceIdToDelete);
        const $btn = $(this);
        $btn.prop('disabled', true).text('DELETING...');

        $.ajax({
            url: `/admin/services/${serviceIdToDelete}`,
            type: 'DELETE',
            success: function(res) {
                console.log('Delete success:', res);
                closeDeleteModal();
                showToast('Service removed', 'success');
                setTimeout(() => window.location.href = '{{ route('admin.services.index') }}', 800);
            },
            error: function(xhr) {
                console.error('Delete failed:', xhr.responseText);
                $btn.prop('disabled', false).text('Yes, Delete');
                showToast('Action failed', 'error');
            }
        });
    });

    // --- Modal Logic ---
    window.openEditModalFromShow = function(btn) {
        const $btn = $(btn);
        $('#edit_service_id').val($btn.data('id'));
        $('#edit_api_service_id_hidden').val($btn.data('api_service_id'));
        $('#edit_name').val($btn.data('name'));
        $('#edit_api_provider_id').val($btn.data('api_provider_id'));
        $('#edit_category_id').val($btn.data('category_id'));
        $('#edit_type').val(($btn.data('type') || 'default').toString().toLowerCase());
        $('#edit_rate').val($btn.data('provider_rate') ?? $btn.data('rate'));
        $('#edit_sale_price').val($btn.data('sale_price'));
        $('#edit_price_locked').prop('checked', $btn.data('price_locked') == '1');
        $('#edit_min_order').val($btn.data('min_order'));
        $('#edit_max_order').val($btn.data('max_order'));
        $('#edit_description').val($btn.data('description'));
        $('#edit_status').val(($btn.data('status') || 'active').toString().toLowerCase());
        
        $('#edit_dripfeed').prop('checked', $btn.data('dripfeed') == '1');
        $('#edit_refill').prop('checked', $btn.data('refill') == '1');
        $('#edit_cancel').prop('checked', $btn.data('cancel') == '1');

        const updateProfit = () => {
            const providerRate = parseFloat($('#edit_rate').val()) || 0;
            const salePrice = parseFloat($('#edit_sale_price').val()) || 0;
            const profit = salePrice - providerRate;
            $('#edit_profit_per_k').text('$' + profit.toFixed(4));
        };
        updateProfit();
        $('#edit_rate, #edit_sale_price').off('input.syncprofit').on('input.syncprofit', updateProfit);

        $('#editServiceModal').removeClass('hidden');
        setTimeout(() => $('#editModalContainer').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    }

    window.closeEditModal = function() {
        $('#editModalContainer').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#editServiceModal').addClass('hidden'), 300);
    }

    $('#updateServiceBtn').on('click', function() {
        const id = $('#edit_service_id').val();
        
        // Prepare payload - ensure types match what Laravel expects
        const payload = {
            api_provider_id: $('#edit_api_provider_id').val() || null,
            api_service_id: $('#edit_api_service_id_hidden').val() || '0', 
            category_id: $('#edit_category_id').val(),
            name: $('#edit_name').val(),
            type: $('#edit_type').val() || 'default',
            rate: parseFloat($('#edit_rate').val()) || 0,
            price_per_k: parseFloat($('#edit_sale_price').val()) || 0,
            sale_price: parseFloat($('#edit_sale_price').val()) || 0,
            price_locked: $('#edit_price_locked').is(':checked') ? 1 : 0,
            min_order: parseInt($('#edit_min_order').val()) || 1,
            max_order: parseInt($('#edit_max_order').val()) || 1000,
            description: $('#edit_description').val(),
            status: $('#edit_status').val() || 'active',
            drip_feed: $('#edit_dripfeed').is(':checked') ? 1 : 0,
            refill: $('#edit_refill').is(':checked') ? 1 : 0,
            cancel: $('#edit_cancel').is(':checked') ? 1 : 0
        };

        const $btn = $(this);
        $btn.prop('disabled', true).text('SAVING...');

        $.ajax({
            url: `/admin/services/${id}`,
            type: 'PUT',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function(res) {
                closeEditModal();
                showToast('Service updated successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Save Changes');
                const errMsg = xhr.responseJSON?.message || 'Update failed';
                showToast(errMsg, 'error');
                
                // Log detailed validation errors if exist
                if (xhr.responseJSON?.errors) {
                    console.error('Validation errors:', xhr.responseJSON.errors);
                }
            }
        });
    });
</script>
@endsection
