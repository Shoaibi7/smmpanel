@extends('layouts.admin')

@section('title', 'API Provider Details')
@section('page_title', $provider->api_name)

@section('content')
<div class="space-y-6">
    <!-- Header with Action Buttons -->
    <div class="flex justify-between items-start">
        <div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $provider->api_name }}</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ $provider->short_name }}</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="openEditModal(this)" 
                data-id="{{ $provider->id }}"
                data-api_name="{{ addslashes($provider->api_name) }}"
                data-short_name="{{ addslashes($provider->short_name) }}"
                data-api_url="{{ addslashes($provider->api_url) }}"
                data-status="{{ $provider->status }}"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                Edit
            </button>
            <a href="{{ route('admin.api-providers.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Back
            </a>
        </div>
    </div>

    <!-- Provider Info Grid -->
    <div class="grid grid-cols-4 gap-4">
        <!-- Balance Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-gray-600 dark:text-gray-400 text-sm mb-2">Current Balance</div>
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-4">${{ $provider->formatted_balance }}</div>
            <button onclick="syncBalance({{ $provider->id }})" class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition">
                Sync Balance
            </button>
        </div>

        <!-- Services Count Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-gray-600 dark:text-gray-400 text-sm mb-2">Services</div>
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-4">{{ $provider->db_services_count ?? $provider->services()->count() }}</div>
            <button onclick="syncServices({{ $provider->id }})" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition">
                Sync Rates
            </button>
        </div>

        <!-- Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-gray-600 dark:text-gray-400 text-sm mb-2">Status</div>
            <div class="text-3xl font-bold @if($provider->status === 'enabled') text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif mb-4">
                {{ ucfirst($provider->status) }}
            </div>
            <button onclick="toggleStatus({{ $provider->id }})" class="w-full px-3 py-2 @if($provider->status === 'enabled') bg-red-600 hover:bg-red-700 @else bg-green-600 hover:bg-green-700 @endif text-white rounded text-sm transition">
                Toggle
            </button>
        </div>

        <!-- Last Sync Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-gray-600 dark:text-gray-400 text-sm mb-2">Last Synced</div>
            <div class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
                @if($provider->last_sync_at)
                    {{ $provider->last_sync_at->format('M d, Y H:i') }}
                @else
                    Never
                @endif
            </div>
            <button onclick="testConnection({{ $provider->id }})" class="w-full px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm transition">
                Test Connection
            </button>
        </div>
    </div>

    <!-- Provider Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Provider Configuration</h3>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <span class="text-gray-600 dark:text-gray-400 text-sm">API URL:</span>
                <p class="font-mono text-sm text-gray-800 dark:text-gray-200 break-all">{{ $provider->api_url }}</p>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400 text-sm">API Key:</span>
                <p class="font-mono text-sm text-gray-500">[Encrypted]</p>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400 text-sm">Created:</span>
                <p class="text-gray-800 dark:text-gray-200">{{ $provider->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400 text-sm">Updated:</span>
                <p class="text-gray-800 dark:text-gray-200">{{ $provider->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Associated Services ({{ $services->total() }})</h3>
            <a href="{{ route('admin.services.bulk-import', $provider) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm">
                + Import Services
            </a>
        </div>

        @if($services->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600 dark:text-gray-400 font-semibold">Service Name</th>
                            <th class="px-4 py-3 text-left text-gray-600 dark:text-gray-400 font-semibold">Type</th>
                            <th class="px-4 py-3 text-left text-gray-600 dark:text-gray-400 font-semibold">Category</th>
                            <th class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 font-semibold">Rate</th>
                            <th class="px-4 py-3 text-center text-gray-600 dark:text-gray-400 font-semibold">Min/Max</th>
                            <th class="px-4 py-3 text-center text-gray-600 dark:text-gray-400 font-semibold">Status</th>
                            <th class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($services as $service)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.services.show', $service) }}" class="text-brand-600 hover:text-brand-700 font-medium">
                                        {{ $service->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">{{ $service->type }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $service->category?->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">
                                    ${{ number_format($service->rate, 4) }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400 text-xs">
                                    {{ $service->min_order }}/{{ $service->max_order }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs @if($service->status === 'active') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 @else bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 @endif">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.services.show', $service) }}" class="text-gray-600 hover:text-gray-700 text-sm">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $services->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">No services imported yet. <a href="{{ route('admin.services.bulk-import', $provider) }}" class="text-brand-600 hover:text-brand-700">Import services</a></p>
            </div>
        @endif
    </div>
</div>

@include('admin.api-providers._provider_modal')


@endsection

@section('scripts')
<script>
    function syncBalance(providerId) {
        showLoading();
        makeRequest(`/admin/api-providers/${providerId}/sync-balance`, 'POST', {})
            .then(response => {
                hideLoading();
                showToast(response.message, 'success');
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                hideLoading();
                showToast(error.message || 'Error syncing balance', 'error');
            });
    }

    function syncServices(providerId) {
        showLoading();
        makeRequest(`/admin/api-providers/${providerId}/sync-services`, 'POST', {})
            .then(response => {
                hideLoading();
                showToast(response.message, 'success');
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                hideLoading();
                showToast(error.message || 'Error syncing services', 'error');
            });
    }

    function toggleStatus(providerId) {
        showLoading();
        makeRequest(`/admin/api-providers/${providerId}/toggle`, 'POST', {})
            .then(response => {
                hideLoading();
                showToast(response.message, 'success');
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                hideLoading();
                showToast(error.message || 'Error toggling status', 'error');
            });
    }

    function testConnection(providerId) {
        showLoading();
        makeRequest(`/admin/api-providers/${providerId}/test-connection`, 'POST', {})
            .then(response => {
                hideLoading();
                showToast(response.message, 'success');
            })
            .catch(error => {
                hideLoading();
                showToast(error.message || 'Connection test failed', 'error');
            });
    }
</script>
@endsection
