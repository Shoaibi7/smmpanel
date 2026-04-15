
@extends('layouts.admin')

@section('title', 'Services')
@section('page_title', 'Services Management')

@section('content')
<div class="space-y-6">
    <!-- Header & Tabs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-secondary-200 dark:border-secondary-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-lg font-black text-secondary-900 dark:text-white tracking-tight">Services Management</h1>
                @if(request('api_provider_id'))
                    <span class="px-2 py-0.5 bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-orange-200 dark:border-orange-800 flex items-center gap-2">
                        Filtered by Provider
                        <a href="{{ route('admin.services.index') }}" class="hover:text-orange-800 dark:hover:text-orange-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                @endif
                @if(request('search'))
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-blue-200 dark:border-blue-800 flex items-center gap-2">
                        Search: "{{ request('search') }}"
                        <a href="{{ route('admin.services.index', request()->except('search')) }}" class="hover:text-blue-800 dark:hover:text-blue-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                @endif
            </div>
            <p class="text-[9px] uppercase font-bold text-secondary-400 tracking-wider mt-1">Manage your internal and API services</p>
        </div>
        
        <!-- Styled Tabs -->
        <div class="flex items-center gap-2 bg-secondary-100 dark:bg-secondary-800 p-1 rounded-xl">
            <button onclick="switchTab('local')" id="tab-local" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all shadow-sm bg-white dark:bg-secondary-700 text-orange-600 dark:text-orange-500">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    My Panel Services
                </div>
            </button>
            <button onclick="switchTab('api')" id="tab-api" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all text-secondary-500 hover:text-secondary-700 dark:hover:text-secondary-300">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                    Import from API
                </div>
            </button>
        </div>
    </div>

    <!-- API TAB SECTION -->
    <div id="section-api" class="hidden animate-in fade-in slide-in-from-bottom-2 duration-300">
        <!-- API Provider Service Import Section -->
        <div class="bg-white dark:bg-secondary-900 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 p-5 mb-4">
            <div class="flex flex-col sm:flex-row items-end gap-4">
                <div class="flex-1 w-full">
                    <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest mb-2 ml-1">Select API Source</label>
                    <div class="relative">
                        <select id="api-provider-select" class="w-full pl-4 pr-10 py-3 text-[10px] font-bold border border-secondary-200 dark:border-secondary-700 rounded-xl bg-secondary-50 dark:bg-secondary-800 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none appearance-none bg-none cursor-pointer">
                            <option value="">Choose Provider...</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->api_name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-secondary-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
                <div class="w-full sm:w-auto">
                    <x-button type="button" id="fetch-api-services-btn" variant="primary" icon="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        FETCH API LIST
                    </x-button>
                </div>
            </div>
            
            <div id="api-services-table-wrapper" class="mt-8 hidden animate-in zoom-in-95 duration-300">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 px-1">
                    <h4 class="text-xs font-black text-secondary-900 dark:text-white flex items-center uppercase tracking-tight">
                        <span class="w-2 h-5 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full mr-3 shadow-sm"></span>
                        API Services List
                    </h4>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <div class="relative w-full sm:w-64">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" id="apiServicesRealtimeSearch" placeholder="Search API services..." 
                                class="w-full pl-8 pr-4 py-2 text-[9px] font-bold border border-secondary-200 dark:border-secondary-700 rounded-lg bg-secondary-50 dark:bg-secondary-800 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none">
                        </div>
                        <div class="px-3 py-1 bg-orange-50 dark:bg-orange-900/20 rounded-full text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest border border-orange-100 dark:border-orange-800 whitespace-nowrap">
                            <span id="api-services-count">0</span> RESULTS
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-secondary-200 dark:border-secondary-800 shadow-sm">
                    <table class="min-w-full divide-y divide-secondary-100 dark:divide-secondary-800" id="api-services-table">
                        <thead class="bg-secondary-50 dark:bg-secondary-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-[8px] font-black text-secondary-400 uppercase tracking-widest">ID</th>
                                <th class="px-4 py-3 text-left text-[8px] font-black text-secondary-400 uppercase tracking-widest">Service & Category</th>
                                <th class="px-4 py-3 text-left text-[8px] font-black text-secondary-400 uppercase tracking-widest">Rate</th>
                                <th class="px-4 py-3 text-center text-[8px] font-black text-secondary-400 uppercase tracking-widest">Avg Time</th>
                                <th class="px-4 py-3 text-center text-[8px] font-black text-secondary-400 uppercase tracking-widest">Min/Max</th>
                                <th class="px-4 py-3 text-right text-[8px] font-black text-secondary-400 uppercase tracking-widest">Action</th>
                            </tr>
                        </thead>
                        <tbody id="api-services-table-body" class="bg-white dark:bg-secondary-800 divide-y divide-secondary-50 dark:divide-secondary-800">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- LOCAL TAB SECTION -->
    <div id="section-local" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
        
        <!-- Real-time Search -->
        <div class="bg-white dark:bg-secondary-900 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 p-2 mb-4">
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-secondary-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" id="serviceRealtimeSearch" placeholder="Search by name, ID, or category..."
                    class="w-full pl-11 pr-4 py-3 text-[10px] border-none rounded-xl bg-transparent text-secondary-900 dark:text-white font-medium focus:ring-0 placeholder-secondary-400">
                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                    <div class="px-2 py-1 bg-secondary-100 dark:bg-secondary-800 rounded text-[8px] font-black text-secondary-400 uppercase tracking-wider">
                        CMD + K
                    </div>
                </div>
            </div>
        </div>


    <!-- Services Table -->
    <div class="space-y-4">
        @if($services->count() > 0)
            <!-- Table Header -->
            <div class="hidden lg:grid lg:grid-cols-12 gap-4 px-3 py-1.5 bg-secondary-50/50 dark:bg-secondary-900/30 rounded-xl border border-secondary-100 dark:border-secondary-800">
                <div class="col-span-1 flex items-center">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-secondary-300 text-orange-600 focus:ring-orange-500" onclick="toggleAllCheckboxes(this)">
                </div>
                <div class="col-span-4 text-[8px] uppercase font-black text-secondary-400 tracking-widest flex items-center">Service Details</div>
                <div class="col-span-2 text-[8px] uppercase font-black text-secondary-400 tracking-widest flex items-center">Provider</div>
                <div class="col-span-2 text-[8px] uppercase font-black text-secondary-400 tracking-widest flex items-center text-right justify-end">Cost / Sale Price</div>
                <div class="col-span-2 text-[8px] uppercase font-black text-secondary-400 tracking-widest flex items-center justify-center">Status</div>
                <div class="col-span-1 text-[8px] uppercase font-black text-secondary-400 tracking-widest flex items-center justify-end">Actions</div>
            </div>

            <!-- Table Rows -->
            <div class="space-y-2" id="servicesTableBody">
                @foreach($services as $service)
                    <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-sm border border-secondary-100 dark:border-secondary-700 hover:shadow-md hover:border-orange-200 dark:hover:border-orange-900/30 transition-all duration-200 group relative">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 p-2.5 items-center">
                            
                            <!-- Checkbox -->
                            <div class="col-span-1 flex items-center">
                                <input type="checkbox" class="service-checkbox w-4 h-4 rounded border-secondary-300 text-orange-600 focus:ring-orange-500" value="{{ $service->id }}">
                            </div>

                            <!-- Service Details -->
                            <div class="col-span-1 lg:col-span-4">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black bg-secondary-100 text-secondary-600 dark:bg-secondary-700 dark:text-secondary-300">ID: {{ $service->id }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800">
                                            {{ str_replace('_', ' ', $service->type) }}
                                        </span>
                                    </div>
                                    <a href="{{ route('admin.services.show', $service) }}" class="text-[10px] font-bold text-secondary-900 dark:text-white group-hover:text-orange-600 transition-colors line-clamp-2 leading-snug">
                                        {{ $service->name }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[9px] text-secondary-500 font-medium">{{ $service->category?->name ?? 'Uncategorized' }}</span>
                                        @if($service->dripfeed || $service->drip_feed)
                                            <span class="px-1.5 py-0.5 rounded-full bg-purple-50 text-purple-600 text-[8px] font-bold uppercase tracking-wider border border-purple-100">Dripfeed</span>
                                        @endif
                                        @if($service->refill)
                                            <span class="px-1.5 py-0.5 rounded-full bg-green-50 text-green-600 text-[8px] font-bold uppercase tracking-wider border border-green-100">Refill</span>
                                        @endif
                                        @if($service->average_time)
                                            <span class="px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[8px] font-bold uppercase tracking-wider border border-blue-100">
                                                <svg class="w-2 h-2 inline-block mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $service->average_time }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Provider -->
                            <div class="col-span-1 lg:col-span-2">
                                @if($service->apiProvider)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded bg-orange-100 dark:bg-orange-900/30 text-orange-600 flex items-center justify-center text-[9px] font-bold">
                                            {{ substr($service->apiProvider->api_name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-secondary-900 dark:text-white">{{ $service->apiProvider->api_name }}</span>
                                            <span class="text-[9px] text-secondary-400">ID: {{ $service->api_service_id }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-secondary-100 dark:bg-secondary-700 text-secondary-500 text-[9px] font-bold uppercase tracking-wider">
                                        Internal
                                    </span>
                                @endif
                            </div>

                            <!-- Rate / Sale Price -->
                            <div class="col-span-1 lg:col-span-2 text-right">
                                <div class="flex flex-col items-end">
                                    <div class="text-[10px] font-black text-secondary-900 dark:text-white">
                                        {{ format_currency($service->rate ?? 0) }}
                                    </div>
                                    @php
                                        $sale = $service->sale_price ?? $service->price_per_k;
                                    @endphp
                                    <div class="text-[9px] font-bold text-orange-600 dark:text-orange-400 mt-0.5">
                                        {{ format_currency($sale) }} Sale Price
                                    </div>
                                    <div class="text-[8px] text-secondary-400 mt-0.5">per 1000</div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-span-1 lg:col-span-2 flex justify-center">
                                @php
                                    $isActive = $service->status === 'active' || $service->is_active;
                                @endphp
                                <div class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-wider border {{ $isActive ? 'bg-green-50 text-green-600 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-red-50 text-red-600 border-red-200 dark:bg-red-900/20 dark:border-red-800' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $isActive ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                    {{ $isActive ? 'Active' : 'Disabled' }}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="col-span-1 lg:col-span-1 flex justify-end">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button type="button" class="p-1.5 rounded-lg bg-secondary-100 dark:bg-secondary-700 text-secondary-600 dark:text-secondary-300 hover:bg-secondary-200 dark:hover:bg-secondary-600 transition-all">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-item onclick="openEditModal(this)" 
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
                                            data-status="{{ $isActive ? 'active' : 'inactive' }}">
                                            <x-slot name="icon">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </x-slot>
                                            Edit Service
                                        </x-dropdown-item>
                                        <x-dropdown-item onclick="window.location.href='{{ route('admin.services.show', $service) }}'">
                                            <x-slot name="icon">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </x-slot>
                                            View Details
                                        </x-dropdown-item>
                                        <div class="border-t border-secondary-100 dark:border-secondary-700 my-1"></div>
                                        <x-dropdown-item onclick="deleteService({{ $service->id }}, '{{ $service->name }}')" class="text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <x-slot name="icon">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </x-slot>
                                            Delete Service
                                        </x-dropdown-item>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Bulk Actions & Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4 px-1">
                <div class="flex gap-2 items-center bg-white dark:bg-secondary-800 p-1.5 rounded-xl border border-secondary-100 dark:border-secondary-700 shadow-sm">
                    <span class="text-[9px] font-black uppercase tracking-widest text-secondary-400 bg-secondary-100 dark:bg-secondary-700 px-3 py-1.5 rounded-lg">
                        <span id="checkedCount">0</span> Selected
                    </span>
                    <button onclick="bulkEnable()" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm shadow-green-500/10 transition-all active:scale-95">
                        Enable
                    </button>
                    <button onclick="bulkDisable()" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm shadow-red-500/10 transition-all active:scale-95">
                        Disable
                    </button>
                </div>

                <div class="flex-1 flex justify-end">
                    {{ $services->onEachSide(1)->links('vendor.pagination.admin') }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-secondary-900 rounded-2xl border border-dashed border-secondary-200 dark:border-secondary-700">
                <div class="w-16 h-16 rounded-2xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center mb-4 shadow-sm">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <h3 class="text-base font-black text-secondary-900 dark:text-white mb-2">No Services Found</h3>
                <p class="text-xs text-secondary-500 dark:text-secondary-400 text-center max-w-sm mb-6">
                    You haven't added any services yet. Import them from an API provider or create one manually.
                </p>
                <button onclick="switchTab('api')" class="px-6 py-2.5 bg-secondary-900 dark:bg-white text-white dark:text-secondary-900 rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-secondary-800 dark:hover:bg-secondary-100 transition-all shadow-md">
                    Import Services
                </button>
            </div>
        @endif
    </div> <!-- End section-local -->

</div> <!-- End main container -->





<!-- Add Service Modal -->
<div id="addServiceModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm hidden animate-in fade-in duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg p-0 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modalContainer">
        <!-- Modal Header -->
        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                <span class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </span>
                Add Service to Panel
            </h3>
            <button id="closeAddServiceModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Select Category</label>
                    <select id="modal_category_id" class="w-full px-4 py-2 text-[10px] font-bold border border-secondary-200 dark:border-secondary-700 rounded-xl bg-secondary-50 dark:bg-secondary-800 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none appearance-none cursor-pointer">
                        <option value="">Choose Category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <div class="mt-1 text-[8px] font-bold text-secondary-400 uppercase tracking-widest">Provider's Category: <span id="modalCategoryName" class="text-secondary-600 dark:text-secondary-300 italic"></span></div>
                </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Service Name</label>
                <div id="modalName" class="px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-900/30 text-gray-800 dark:text-gray-200 border border-gray-100 dark:border-gray-700 text-xs font-medium"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Provider Rate/1k</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                        <div id="modalOriginalPrice" class="pl-8 pr-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-900/30 text-gray-800 dark:text-gray-200 border border-gray-100 dark:border-gray-700 text-xs font-bold"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Price Markup (%)</label>
                    <div class="relative">
                        <input type="number" id="modalPercent" class="w-full pl-4 pr-10 py-2 border border-gray-100 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900/30 text-gray-800 dark:text-white text-xs font-bold focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" placeholder="e.g. 50">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">%</span>
                    </div>
                </div>
            </div>

            <div class="bg-orange-50 dark:bg-orange-900/10 rounded-2xl p-4 border border-orange-100 dark:border-orange-900/30">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[9px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-1">Projected Selling Price</div>
                        <div class="text-xl font-black text-orange-700 dark:text-orange-400 flex items-center">
                            <span class="mr-1">$</span>
                            <span id="modalSalePrice">0.0000</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-[9px] font-bold text-orange-600/60 dark:text-orange-400/60 uppercase tracking-widest mb-1">Calculated Margin</div>
                        <div id="modalProfit" class="text-base font-bold text-orange-600 dark:text-orange-400">$0.0000</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
            <button id="cancelAddServiceModal" class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold text-xs hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                Discard
            </button>
            <button id="saveAddServiceModal" class="px-8 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold text-xs shadow-lg shadow-orange-500/20 active:scale-95 transition-all">
                Add to Services
            </button>
        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div id="editServiceModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm hidden animate-in fade-in duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg p-0 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="editModalContainer">
        <!-- Modal Header -->
        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-base font-black text-gray-800 dark:text-white flex items-center uppercase tracking-tight">
                <span class="w-8 h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center mr-3 shadow-lg shadow-orange-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </span>
                Edit Service
            </h3>
            <button onclick="closeEditModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
            <form id="editServiceForm" class="space-y-4">
                <input type="hidden" id="edit_service_id">
                <input type="hidden" id="edit_api_service_id_hidden">
                
                <div class="grid grid-cols-2 gap-4">
                    <!-- Provider & Category -->
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">API Provider</label>
                        <select id="edit_api_provider_id" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            <option value="">Internal</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->api_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Category</label>
                        <select id="edit_category_id" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Name -->
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Service Name</label>
                        <input type="text" id="edit_name" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none" placeholder="Enter service name">
                    </div>

                    <!-- Type & Rate -->
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Service Type</label>
                        <select id="edit_type" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            <option value="default">Default</option>
                            <option value="subscriptions">Subscriptions</option>
                            <option value="custom_comments">Custom Comments</option>
                            <option value="mentions">Mentions</option>
                            <option value="package">Package</option>
                            <option value="comment_likes">Comment Likes</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Rate (per 1K)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-[10px]">$</span>
                            <input type="number" id="edit_rate" step="0.0001" class="w-full pl-7 pr-3 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-black focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Sale Price (per 1K)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-[10px]">$</span>
                            <input type="number" id="edit_sale_price" step="0.0001" class="w-full pl-7 pr-3 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-black focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                        </div>
                    </div>

                    <div class="col-span-2 bg-gray-50/50 dark:bg-gray-900/30 rounded-xl p-3 border border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="edit_price_locked" class="w-3.5 h-3.5 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="ml-2 text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Lock Sale Price</span>
                        </label>
                        <div class="text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">
                            Profit / 1K: <span id="edit_profit_per_k" class="text-gray-900 dark:text-white">$0.0000</span>
                        </div>
                    </div>

                    <!-- Min & Max -->
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Min Order</label>
                        <input type="number" id="edit_min_order" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Max Order</label>
                        <input type="number" id="edit_max_order" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-bold focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                    </div>

                    <!-- Features -->
                    <div class="col-span-2 bg-gray-50/50 dark:bg-gray-900/30 rounded-xl p-3 border border-gray-100 dark:border-gray-700">
                        <div class="grid grid-cols-3 gap-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="edit_dripfeed" class="w-3.5 h-3.5 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                <span class="ml-2 text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Dripfeed</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="edit_refill" class="w-3.5 h-3.5 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                <span class="ml-2 text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Refill</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="edit_cancel" class="w-3.5 h-3.5 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                <span class="ml-2 text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Cancel</span>
                            </label>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Service Status</label>
                        <select id="edit_status" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-black focus:ring-2 focus:ring-orange-500 transition-all outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Description</label>
                        <textarea id="edit_description" rows="2" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-medium focus:ring-2 focus:ring-orange-500 transition-all outline-none resize-none" placeholder="Service details..."></textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
            <button onclick="closeEditModal()" class="px-6 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 font-black text-[9px] uppercase tracking-widest hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                Cancel
            </button>
            <button id="updateServiceBtn" class="px-8 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-black text-[9px] uppercase tracking-widest shadow-lg shadow-orange-500/20 active:scale-95 transition-all">
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteServiceModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm hidden animate-in fade-in duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-0 overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="deleteModalContainer">
        <div class="p-8 text-center">
            <div class="w-16 h-16 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-red-500/10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-black text-gray-800 dark:text-white uppercase tracking-tight mb-2">Are you sure?</h3>
            <p class="text-[10px] text-secondary-500 dark:text-secondary-400 font-bold leading-relaxed mb-6">
                You are about to delete <span id="delete_service_name" class="text-red-600 font-black"></span>. This action cannot be undone.
            </p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-6 py-2.5 rounded-xl border border-gray-100 dark:border-gray-700 text-gray-500 font-black text-[9px] uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    No, Cancel
                </button>
                <button id="confirmDeleteBtn" class="flex-1 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-black text-[9px] uppercase tracking-widest shadow-lg shadow-red-500/20 active:scale-95 transition-all">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
    // --- Tab Switching Logic ---
    window.switchTab = function(type) {
        // Base classes for both states
        const activeClasses = 'shadow-sm bg-white dark:bg-secondary-700 text-orange-600 dark:text-orange-500';
        const inactiveClasses = 'text-secondary-500 hover:text-secondary-700 dark:hover:text-secondary-300';
        
        if (type === 'api') {
            $('#section-api').removeClass('hidden');
            $('#section-local').addClass('hidden');
            
            $('#tab-api').addClass(activeClasses).removeClass(inactiveClasses);
            $('#tab-local').removeClass(activeClasses).addClass(inactiveClasses);
        } else {
            $('#section-local').removeClass('hidden');
            $('#section-api').addClass('hidden');
            
            $('#tab-local').addClass(activeClasses).removeClass(inactiveClasses);
            $('#tab-api').removeClass(activeClasses).addClass(inactiveClasses);
        }
    }



    // --- Bulk Actions ---
    window.toggleAllCheckboxes = function(element) {
        $('.service-checkbox').prop('checked', element.checked);
        updateCheckedCount();
    }

    function updateCheckedCount() {
        const count = $('.service-checkbox:checked').length;
        $('#checkedCount').text(count);
    }

    $(document).on('change', '.service-checkbox', updateCheckedCount);

    window.bulkEnable = function() {
        const ids = $('.service-checkbox:checked').map(function() { return $(this).val(); }).get();
        if (!ids.length) return showToast('Select services first', 'error');

        showToast('Enabling...', 'info');
        $.ajax({
            url: '/admin/services/bulk-enable',
            type: 'POST',
            data: JSON.stringify({ service_ids: ids }),
            contentType: 'application/json',
            success: function(res) {
                showToast(res.message, 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) { showToast(xhr.responseJSON?.message || 'Error', 'error'); }
        });
    }

    window.bulkDisable = function() {
        const ids = $('.service-checkbox:checked').map(function() { return $(this).val(); }).get();
        if (!ids.length) return showToast('Select services first', 'error');

        showToast('Disabling...', 'info');
        $.ajax({
            url: '/admin/services/bulk-disable',
            type: 'POST',
            data: JSON.stringify({ service_ids: ids }),
            contentType: 'application/json',
            success: function(res) {
                showToast(res.message, 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) { showToast(xhr.responseJSON?.message || 'Error', 'error'); }
        });
    }


    var serviceIdToDelete = null;

    window.deleteService = function(id, name) {
        console.log('Preparing to delete service (Index):', id, name);
        serviceIdToDelete = id;
        $('#delete_service_name').text(name);
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
                showToast('Service deleted successfully', 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                console.error('Delete failed:', xhr.responseText);
                $btn.prop('disabled', false).text('Yes, Delete');
                showToast(xhr.responseJSON?.message || 'Delete failed', 'error');
            }
        });
    });

    // --- API Service Fetching ---
    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const fetchBtnDefaultLabel = 'FETCH API LIST';

    $(document).on('click', '#fetch-api-services-btn', function() {
        const providerId = $('#api-provider-select').val();
        if (!providerId) {
            showToast('Select a provider', 'error');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).text('FETCHING...');

        $.ajax({
            url: '/admin/services/fetch-api-services',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            data: { provider_id: providerId },
            dataType: 'json',
            timeout: 45000,
            success: function(data) {
                if (data?.success) {
                    renderApiServicesTable(data.services || []);
                    if (data.cached) {
                        showToast(data.message || 'Showing cached services', 'info');
                    }
                } else {
                    showToast(data?.message || 'Fetch failed', 'error');
                }
            },
            error: function(xhr, status) {
                if (status === 'timeout') {
                    showToast('Provider is slow. Please try again.', 'error');
                    return;
                }
                if (xhr?.status === 419) {
                    showToast('Session expired. Please refresh page.', 'error');
                    return;
                }
                showToast(xhr?.responseJSON?.message || 'Fetch failed', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text(fetchBtnDefaultLabel);
            }
        });
    });

    var currentApiServices = [];
    var currentModalService = null;

    function renderApiServicesTable(services) {
        currentApiServices = services;
        const $tbody = $('#api-services-table-body');
        $tbody.empty();
        $('#api-services-count').text(services.length);

        services.forEach((srv, idx) => {
            const features = [];
            if (srv.dripfeed) features.push('<span class="text-[8px] mr-1">DF</span>');
            if (srv.refill) features.push('<span class="text-[8px] mr-1">RF</span>');
            
            $tbody.append(`
                <tr class="hover:bg-secondary-50 dark:hover:bg-secondary-700/50 transition-colors border-b border-secondary-50 dark:border-secondary-800 last:border-0">
                    <td class="px-4 py-3 text-[10px] font-mono text-secondary-600 dark:text-secondary-400 font-bold">${srv.service_id || srv.service}</td>
                    <td class="px-4 py-3">
                        <div class="text-[10px] font-black text-secondary-900 dark:text-white line-clamp-1">${srv.name}</div>
                        <div class="text-[8px] text-secondary-500 uppercase font-bold tracking-wider mt-0.5">${srv.category}</div>
                    </td>
                    <td class="px-4 py-3 text-[10px] font-black text-secondary-900 dark:text-white">$${parseFloat(srv.rate).toFixed(4)}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[8px] font-black border border-blue-100 dark:border-blue-800">
                            ${srv.average_time || 'N/A'}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-[9px] font-bold text-secondary-500">${srv.min} / ${srv.max}</td>
                    <td class="px-4 py-3 text-right">
                        <button class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-sm shadow-orange-500/20 transition-all active:scale-95" onclick="openAddModal(${idx})">ADD</button>
                    </td>
                </tr>
            `);
        });
        $('#api-services-table-wrapper').removeClass('hidden');
    }

    window.openAddModal = function(idx) {
        currentModalService = currentApiServices[idx];
        $('#modalCategoryName').text(currentModalService.category);
        $('#modalName').text(currentModalService.name);
        $('#modalOriginalPrice').text(parseFloat(currentModalService.rate || 0).toFixed(4));
        
        // Auto-select category if it matches the provider's category name
        $('#modal_category_id option').each(function() {
            if ($(this).text().trim().toLowerCase() === currentModalService.category.trim().toLowerCase()) {
                $('#modal_category_id').val($(this).val());
                return false;
            }
        });

        // Set default markup from settings
        const defaultMarkup = {{ $defaultMarkup ?? 0 }};
        $('#modalPercent').val(defaultMarkup);

        $('#addServiceModal').removeClass('hidden');
        setTimeout(() => $('#modalContainer').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
        calculatePrice();
    }

    const currencySymbol = '{{ get_currency_code() === "PKR" ? "Rs " : "$" }}';
    const currencyRate = {{ get_conversion_rate() }};

    function calculatePrice() {
        if (!currentModalService) return;
        
        // Ensure we handle different number formats if needed
        const getVal = (selector) => {
            let v = $(selector).val();
            if (typeof v === 'string') v = v.replace(',', '.');
            return parseFloat(v) || 0;
        };

        const percent = getVal('#modalPercent');
        const baseUSD = parseFloat(currentModalService.rate || 0);
        
        const markupAmountUSD = (baseUSD * percent) / 100;
        const saleUSD = baseUSD + markupAmountUSD;

        const convertedSale = get_currency_code() === 'PKR' ? (saleUSD * currencyRate) : saleUSD;
        const convertedProfit = get_currency_code() === 'PKR' ? (markupAmountUSD * currencyRate) : markupAmountUSD;
        
        // Update display
        $('#modalSalePrice').text(convertedSale.toFixed(get_currency_code() === 'PKR' ? 2 : 4));
        $('#modalProfit').text(currencySymbol + convertedProfit.toFixed(get_currency_code() === 'PKR' ? 2 : 4));
    }

    // Attach to multiple events to ensure it updates as the user types or pastes
    $(document).on('input keyup change paste', '#modalPercent', calculatePrice);
    window.closeAddServiceModal = function() {
        $('#modalContainer').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#addServiceModal').addClass('hidden'), 300);
        $('#modal_category_id').val(''); // Reset category selection
    };

    $('#closeAddServiceModal, #cancelAddServiceModal').on('click', closeAddServiceModal);

    $('#saveAddServiceModal').on('click', function() {
        if (!currentModalService) return;
        
        const categoryId = $('#modal_category_id').val();
        if (!categoryId) {
            showToast('Please select a category first!', 'error');
            return;
        }

        const percent = parseFloat($('#modalPercent').val()) || 0;
        const base = parseFloat(currentModalService.rate) || 0;
        const sale = base + (base * percent / 100);

        const payload = {
            api_provider_id: $('#api-provider-select').val(),
            api_service_id: currentModalService.service_id || currentModalService.service,
            category_id: categoryId,
            category: currentModalService.category,
            name: currentModalService.name,
            type: currentModalService.type || 'default',
            rate: base.toFixed(6),
            provider_rate: base.toFixed(6),
            price_per_k: sale.toFixed(6),
            sale_price: sale.toFixed(6),
            price_locked: 1,
            min_qty: currentModalService.min || 1,
            max_qty: currentModalService.max || 100000,
            drip_feed: currentModalService.dripfeed ? 1 : 0,
            refill: currentModalService.refill ? 1 : 0,
            average_time: currentModalService.average_time || null,
            description: currentModalService.description || currentModalService.desc || '',
            status: 'active'
        };

        const $btn = $(this);
        $btn.prop('disabled', true).text('SAVING...');

        $.ajax({
            url: '/admin/services',
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function() {
                closeAddServiceModal();
                showToast('Service added successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Add to Services');
                showToast(xhr.responseJSON?.message || 'Error saving service', 'error');
            }
        });
    });

    // --- Edit Service Modal Logic ---
    window.openEditModal = function(btn) {
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
            const providerRateUSD = parseFloat($('#edit_rate').val()) || 0;
            const salePriceUSD = parseFloat($('#edit_sale_price').val()) || 0;
            const profitUSD = salePriceUSD - providerRateUSD;
            
            const convertedProfit = get_currency_code() === 'PKR' ? (profitUSD * currencyRate) : profitUSD;
            $('#edit_profit_per_k').text(currencySymbol + convertedProfit.toFixed(get_currency_code() === 'PKR' ? 2 : 4));
        };
        updateProfit();
        $('#edit_rate, #edit_sale_price').off('input.syncprofit').on('input.syncprofit keyup.syncprofit change.syncprofit paste.syncprofit', updateProfit);

        $('#editServiceModal').removeClass('hidden');
        setTimeout(() => $('#editModalContainer').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    }

    window.closeEditModal = function() {
        $('#editModalContainer').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#editServiceModal').addClass('hidden'), 300);
    }

    $('#updateServiceBtn').on('click', function() {
        const id = $('#edit_service_id').val();
        
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
                showToast(xhr.responseJSON?.message || 'Error updating service', 'error');
            }
        });
    });

    // --- Real-time Table Filter ---
    $('#serviceRealtimeSearch').on('keyup', function(e) {
        if (e.key === 'Enter') {
            window.location.href = `{{ route('admin.services.index') }}?search=${$(this).val()}`;
            return;
        }
        const value = $(this).val().toLowerCase();
        $("#servicesTableBody > div").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // --- API Services Real-time Filter ---
    $(document).on('keyup', '#apiServicesRealtimeSearch', function() {
        const value = $(this).val().toLowerCase();
        $("#api-services-table-body tr").filter(function() {
            const match = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(match);
            return match;
        });
        
        // Update visible count
        const visibleCount = $("#api-services-table-body tr:visible").length;
        $('#api-services-count').text(visibleCount);
    });
</script>
@endsection
