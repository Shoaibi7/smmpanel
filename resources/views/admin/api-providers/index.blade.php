@extends('layouts.admin')

@section('title', 'API Providers')
@section('page_title', 'API Providers Management')

@section('content')
<div class="space-y-6">
    <!-- Quick Actions & Stats -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
             <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center text-white shadow-lg shadow-orange-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-black text-secondary-900 dark:text-white tracking-tight">API Providers</h3>
                <p class="text-[9px] font-bold text-secondary-400 uppercase tracking-widest">Connect and Manage External Panels</p>
            </div>
        </div>
        <x-button type="button" onclick="openCreateModal()" variant="primary" size="sm" class="uppercase tracking-widest">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Provider
        </x-button>
    </div>

    <!-- Real-time Search Section -->
    <div class="bg-white dark:bg-secondary-900 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-800 p-2 mb-4">
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-secondary-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" id="providerRealtimeSearch" placeholder="Search by provider name or short code..." 
                class="w-full pl-11 pr-4 py-3 text-xs border-none rounded-xl bg-transparent text-secondary-900 dark:text-white font-medium focus:ring-0 placeholder-secondary-400">
            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                <div class="px-2 py-1 bg-secondary-100 dark:bg-secondary-800 rounded text-[9px] font-black text-secondary-400 uppercase tracking-wider">
                    CMD + K
                </div>
            </div>
        </div>
    </div>

    <!-- Providers List -->
    <div class="space-y-3">
        @if($providers->count() > 0)
            <!-- Table Header (Hidden on mobile, shown on desktop) -->
            <div class="hidden lg:grid lg:grid-cols-12 gap-4 px-3 py-1.5 bg-secondary-50/50 dark:bg-secondary-900/30 rounded-xl border border-secondary-100 dark:border-secondary-800">
                <div class="col-span-2 text-[9px] uppercase font-black text-secondary-400 tracking-widest">Provider</div>
                <div class="col-span-2 text-[9px] uppercase font-black text-secondary-400 tracking-widest">Balance</div>
                <div class="col-span-1 text-[9px] uppercase font-black text-secondary-400 tracking-widest text-center">Services</div>
                <div class="col-span-2 text-[9px] uppercase font-black text-secondary-400 tracking-widest">Status</div>
                <div class="col-span-2 text-[9px] uppercase font-black text-secondary-400 tracking-widest">Last Sync</div>
                <div class="col-span-3 text-[9px] uppercase font-black text-secondary-400 tracking-widest text-right">Actions</div>
            </div>

            <!-- Provider Cards -->
            <div class="space-y-2" id="providersTable">
                @foreach($providers as $provider)
                    <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-sm border border-secondary-100 dark:border-secondary-700 hover:shadow-md hover:border-orange-200 dark:hover:border-orange-900/30 transition-all duration-200 relative">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 p-2.5 items-center">
                            <div class="col-span-1 lg:col-span-2">
                                <a href="{{ route('admin.api-providers.show', $provider) }}" class="block group">
                                    <div class="flex flex-col">
                                        <h3 class="text-[11px] font-bold text-secondary-900 dark:text-white group-hover:text-orange-600 transition-colors truncate">{{ $provider->api_name }}</h3>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black bg-orange-50 text-orange-600 border border-orange-100 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800 uppercase tracking-wider">{{ $provider->short_name }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Balance Info -->
                            <div class="col-span-1 lg:col-span-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <div class="text-[11px] font-black text-orange-600 dark:text-orange-400" id="balance-{{ $provider->id }}">{{ $provider->formatted_balance }}</div>
                                        <div class="text-[8px] text-secondary-400 font-bold uppercase tracking-wider">{{ $provider->currency ?? 'USD' }}</div>
                                    </div>
                                    <x-icon-button type="button" onclick="refreshBalance({{ $provider->id }})" variant="primary" size="sm" title="Refresh Balance">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </x-icon-button>
                                </div>
                            </div>

                            <!-- Services Count -->
                            <div class="col-span-1 lg:col-span-1">
                                <div class="flex items-center justify-center">
                                    <div class="px-2 py-1 bg-secondary-50 dark:bg-secondary-700/50 rounded-lg border border-secondary-200 dark:border-secondary-600">
                                        <div class="text-xs font-black text-secondary-900 dark:text-white text-center">{{ $provider->db_services_count ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-span-1 lg:col-span-2">
                                <button onclick="toggleStatus({{ $provider->id }})" class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-wider transition-all {{ $provider->status === 'enabled' ? 'bg-orange-50 text-orange-600 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800' : 'bg-red-50 text-red-600 dark:bg-red-900/20 border border-red-200 dark:border-red-800' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $provider->status === 'enabled' ? 'bg-orange-600' : 'bg-red-600' }}"></span>
                                    {{ $provider->status }}
                                </button>
                            </div>

                            <!-- Last Sync -->
                            <div class="col-span-1 lg:col-span-2">
                                <div class="text-[8px] font-bold text-secondary-400 uppercase tracking-wider">Last Sync</div>
                                <div class="text-[10px] font-bold text-secondary-600 dark:text-secondary-300 mt-0.5">
                                    @if($provider->last_sync_at)
                                        {{ $provider->last_sync_at->diffForHumans() }}
                                    @else
                                        <span class="text-red-500">Never</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="col-span-1 lg:col-span-3 flex justify-end">
                                <x-dropdown align="right">
                                    <x-slot name="trigger">
                                        <button type="button" class="p-1.5 rounded-lg bg-secondary-100 dark:bg-secondary-700 text-secondary-600 dark:text-secondary-300 hover:bg-secondary-200 dark:hover:bg-secondary-600 transition-all">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <!-- Edit -->
                                        <x-dropdown-item 
                                            onclick="openEditModal(this)"
                                            data-id="{{ $provider->id }}"
                                            data-api_name="{{ $provider->api_name }}"
                                            data-short_name="{{ $provider->short_name }}"
                                            data-api_url="{{ $provider->api_url }}"
                                            data-currency="{{ $provider->currency }}"
                                            data-status="{{ $provider->status }}">
                                            <x-slot name="icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </x-slot>
                                            Edit Provider
                                        </x-dropdown-item>

                                        <!-- View Details -->
                                        <x-dropdown-item onclick="window.location.href='{{ route('admin.api-providers.show', $provider) }}'">
                                            <x-slot name="icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </x-slot>
                                            View Details
                                        </x-dropdown-item>

                                        <!-- View Services -->
                                        <x-dropdown-item onclick="window.location.href='{{ route('admin.services.index', ['api_provider_id' => $provider->id]) }}'">
                                            <x-slot name="icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                                </svg>
                                            </x-slot>
                                            View Services
                                        </x-dropdown-item>

                                        <!-- Divider -->
                                        <div class="border-t border-secondary-200 dark:border-secondary-600 my-1"></div>

                                        <!-- Delete -->
                                        <x-dropdown-item 
                                            onclick="deleteProvider({{ $provider->id }}, '{{ $provider->api_name }}')"
                                            class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <x-slot name="icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </x-slot>
                                            Delete Provider
                                        </x-dropdown-item>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 bg-white dark:bg-secondary-800 rounded-xl border border-secondary-100 dark:border-secondary-700">
                {{ $providers->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-sm border border-secondary-100 dark:border-secondary-700 p-16 text-center">
                <div class="w-16 h-16 bg-secondary-50 dark:bg-secondary-700/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h4 class="text-sm font-black text-secondary-800 dark:text-white uppercase tracking-tight">No Providers Connected</h4>
                <p class="text-xs text-secondary-500 mt-2 max-w-xs mx-auto">Connect your first API provider to start importing services automatically.</p>
                <x-button type="button" onclick="openCreateModal()" variant="primary" size="lg" class="mt-6 uppercase tracking-widest">
                    Add First Provider
                </x-button>
            </div>
        @endif
    </div>
</div>

<!-- Modal container for Javascript -->
<div id="modal-wrapper"></div>
@endsection

@section('scripts')
<script>
    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function toggleStatus(id) {
        showLoading();
        $.ajax({
            url: `/admin/api-providers/${id}/toggle`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            success: function(res) {
                hideLoading();
                showToast(res.message, 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                hideLoading();
                showToast(xhr.responseJSON?.message || 'Update failed', 'error');
            }
        });
    }

    function refreshBalance(id) {
        const $btn = $(event.currentTarget);
        $btn.addClass('animate-spin');
        
        $.ajax({
            url: `/admin/api-providers/${id}/sync-balance`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            success: function(res) {
                $btn.removeClass('animate-spin');
                if (res.balance) $(`#balance-${id}`).text(res.balance);
                showToast('Balance updated!', 'success');
            },
            error: function(xhr) {
                $btn.removeClass('animate-spin');
                // Check specifically for CSRF error
                if (xhr.status === 419) {
                    showToast('Session expired. Please refresh page.', 'error');
                } else {
                    showToast(xhr.responseJSON?.message || 'Sync failed', 'error');
                }
                console.error('Balance Sync Error:', xhr);
            }
        });
    }

    function deleteProvider(id, name) {
        if (!confirm(`Are you sure you want to delete ${name}?`)) return;
        showLoading();
        
        $.ajax({
            url: `/admin/api-providers/${id}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            success: function() {
                hideLoading();
                showToast('Provider removed', 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                hideLoading();
                showToast('Action failed', 'error');
            }
        });
    }

    // --- Real-time Provider Filter ---
    $('#providerRealtimeSearch').on('keyup', function(e) {
        if (e.key === 'Enter') {
            window.location.href = `{{ route('admin.api-providers.index') }}?search=${$(this).val()}`;
            return;
        }
        const value = $(this).val().toLowerCase();
        $("#providersTable > div").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
</script>

@include('admin.api-providers._provider_modal')
@endsection
